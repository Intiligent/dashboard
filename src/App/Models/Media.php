<?php

namespace App\Models;

use Auth;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Media extends Model
{
    const ENTITY_DEFAULT = 'default';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'user_id',
        'entity',
        'path',
        'title',
        'description',
        'active',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d.m.Y H:i:s',
        'updated_at' => 'datetime:d.m.Y H:i:s',
    ];

    // protected $appends = ['basename', 'humansize', 'dimension', 'icon'];

    /**
     * разрешенные форматы файлов для загрузки
     *
     * @var array
     */
    public static $allowMimeTypes = [
        // images
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'tiff' => 'image/tiff',
        'wbmp' => 'image/vnd.wap.wbmp',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        // documents
        'doc' => 'application/msword',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'pdf' => 'application/pdf',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->setPerPage(60); // (int)config('GALLERY_ITEMS_ON_PAGE', 60)
    }

    /**
     * Transform image path
     *
     * @param $value [image name]
     * @return string
     */
    public function getPathAttribute($value): string
    {
        if (!file_exists(strtok($value, '?'))) {
            return asset('img/default-image.jpg');
        }
        return asset($value);
    }

    /**
     * Image of file type
     *
     * @return string
     */
    public function getIconAttribute(): string
    {
        $extension = pathinfo(strtok($this->getOriginal('path'), '?'), PATHINFO_EXTENSION);
        return asset('img/assets/filetypes/'.$extension.'.png');
    }

    /**
     * Filename
     *
     * @return string
     */
    public function getBasenameAttribute(): string
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    /**
     * File size (human comprehensible)
     *
     * @return string
     */
    public function getHumansizeAttribute(): string
    {
        $path = strtok($this->getOriginal('path'), '?');
        if (is_file($path)) {
            return File::getHumanSize(filesize($path));
        }
        return 'unknown';
    }

    /**
     * Image dimension in px
     *
     * @return null|array
     */
    public function getDimensionAttribute()
    {
        $path = strtok($this->getOriginal('path'), '?');
        try {
            $info = getimagesize($path);
            return ['width' => $info[0], 'height' => $info[1]];
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Get the owning media model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function media()
    {
        return $this->morphTo();
    }

    /**
     * Get the media owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Upload file by link
     *
     * @param array $param
     * @return object Media
     */
    public function uploadFromUrl(array $param = [])
    {
        // проверка на обьязательные поля
        $data = Validator::make($param, [
            'value' => ['required', 'url'],
            'model_id' => ['required', 'numeric', 'min:1'],
            'model_type' => ['required'],
            'entity' => ['sometimes', 'nullable'],
            'path' => ['sometimes'],
            'multiple' => ['required', 'boolean'],
        ])->validate();
        // получить хедеры файла: размер и формат (по ним делать предварительную валидацию)
        $headers = get_headers($data['value'], 1);
        if (isset($headers['Content-Type']) && is_array($headers['Content-Type'])) {
            $headers['Content-Type'] = $headers['Content-Type'][0];
        }
        if (isset($headers['Content-Length']) && is_array($headers['Content-Length'])) {
            $headers['Content-Length'] = max($headers['Content-Length']);
        }
        // список доступных форматов для загрузки
        $allowMimeTypes = array_values(self::$allowMimeTypes);
        // 1. размер не больше 10 мб и добавить в настройки
        // 2. формат из разрешенного списка. добавить в настройки
        $validator = Validator::make($headers, [
            'Content-Length' => ['numeric', 'max:' . pow(1024, 2) * 10],
            'Content-Type' => ['in:' . implode($allowMimeTypes, ',')],
        ], [
            'Content-Length.max' => 'Максимальный размер загружаемого файла :max KB',
            'Content-Type.in' => 'Недопустимый формат файла: ' . $headers['Content-Type'],
        ])->validate();
        // получить параметры файла
        $pathinfo = pathinfo($data['value']);
        // разрешение файла опредленное из mime types по заголовкам файла
        // для безопасности. т.к. может быть исполняемый файл
        $extension = MimeType::search($validator['Content-Type']);
        // получить содержимое файла
        $contents = file_get_contents($data['value']);
        // имя файла. убрать GET параметры из имени файла
        $filename = strtok($pathinfo['filename'], '?');
        $basepath = Arr::get($data, 'path', $data['model_type']);
        // количество файлов с таким названием которое есть в папке
        // $countSameFilename = (int)exec("find storage/$basepath -type f -name '$filename*' | wc -l");
        // if ($countSameFilename > 0) {
        //     $filename .= '(' . ($countSameFilename + 1) . ')';
        // }
        // если в папке назначения есть файл с таким же названием, то добавить uniqid к имени файла
        if (file_exists('storage'.DIRECTORY_SEPARATOR.$basepath.DIRECTORY_SEPARATOR.$filename.'.'.$extension)) {
            $filename .= '-' . uniqid('');
        }
        // путь для сохранения файла
        $destination = $basepath . DIRECTORY_SEPARATOR . $filename . '.' . $extension;
        // сохранение файла
        $store = Storage::disk('public')->put($destination, $contents);
        if (!$store) {
            throw new \Exception('Ошибка записи файла');
        }
        $entity = $data['entity'] ?: self::ENTITY_DEFAULT;
        $modelType = 'App\\Models\\' . ucfirst($data['model_type']);
        // создать запись в галереи
        $model = self::create([
            'model_id' => $data['model_id'],
            'model_type' => $modelType,
            'user_id' => Auth::guard('admin')->id(),
            'entity' => $entity,
            'path' => 'storage'.DIRECTORY_SEPARATOR.$destination,
        ]);
        // в режиме одиночного файла удалять старый файл
        if (!$data['multiple']) {
            $dropIds = self::where('model_id', $data['model_id'])
                ->where('model_type', $modelType)
                ->where('entity', $entity)
                ->where('id', '!=', $model->getAttributes()['id'])
                ->get('id')
                ->pluck('id')
                ->toArray();
            $this->deleteMedia(['id' => $dropIds]);
        }
        return $model->fresh();
    }

    /**
     * Delete media object
     *
     * @param array $param [{id}]
     * @return int [count]
     */
    public function deleteMedia(array $param) :int
    {
        $ids = is_array($param['id']) ? $param['id'] : [$param['id']];
        $collection = self::whereIn('id', $ids);
        $fileDeleted = 0;
        $items = $collection->get();
        foreach ($items as $media) {
            if (file_exists($media->getOriginal('path'))) {
                File::delete($media->getOriginal('path')) && ++$fileDeleted;
            }
        }
        $collection->delete();
        return $fileDeleted;
    }
}
