<?php

namespace Dashboard\Http\Controllers;

use DB;
use Auth;
use Meta;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Exceptions\ExceptionWithMessage;
use App\Models\Media;

class MediaController extends Controller
{
    /**
     * Медиа библиотека файлов
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Support\Facades\View
     */
    public function index(Request $request)
    {
        Meta::set('title', 'Библиотека файлов');
        // SharedData::put($data);
        return view('dashboard::media.library', [
            'item' => $model,
        ]);
    }

    /**
     * Получить все медиа обьекты библиотеки (pagination)
     *
     * @param Request $request
     * @return array
     */
    public function getMediaLibrary(Request $request)
    {
        $collection = Media::orderBy('created_at', 'DESC')->paginate(120);
        return $this->response([DATA => $collection]);
    }

    /**
     * Получить медиа обьекты
     *
     * @param Request $request
     * @return array
     */
    public function getMedia(Request $request)
    {
        $payload = $this->validate($request, [
            'model_id' => ['required', 'numeric', 'min:1'],
            'model_type' => ['required'],
            'entity' => ['sometimes', 'nullable'],
            'multiple' => ['boolean'],
        ]);
        $modelType = 'App\\Models\\' . ucfirst($payload['model_type']);
        $collection = Media::where('model_id', $payload['model_id'])
            ->where('model_type', $modelType)
            ->where('entity', $payload['entity'] ?: Media::ENTITY_DEFAULT)
            ->orderBy('order', 'ASC')
            ->paginate();

        return $this->response([DATA => $collection]);
    }

    /**
     * Загрузка медиа файлов
     *
     * @param Request $request
     * @return array
     */
    public function postMediaUploadFiles(Request $request)
    {
        // список доступных форматов для загрузки
        $allowMimeTypes = array_values(Media::$allowMimeTypes);
        // проверка на обьязательные поля
        $this->validate($request, [
            'files.*' => ['required', 'file', 'mimetypes:'.implode(',', $allowMimeTypes)],
            'model_id' => ['required', 'numeric', 'min:1'],
            'model_type' => ['required'],
            'entity' => ['sometimes', 'nullable'],
            'path' => ['sometimes'],
            'multiple' => ['required', 'boolean'],
        ]);
        $modelType = 'App\\Models\\' . ucfirst($request->get('model_type'));
        $entity = $request->get('entity') ?: Media::ENTITY_DEFAULT;
        $items = [];
        foreach ($request->file('files') as $index => $file) {
            // ограничение на количество загрузок в 10 файлов
            if ($index >= 10) break;
            $basename = $file->getClientOriginalName();
            $directory = $request->get('path') ?: $request->get('model_type');
            // искать в папке назначения имя файла.
            // если оно уже есть то добавлять уникальных хеш (-s4d6de543)
            if (file_exists('storage'.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.$basename)) {
                $pathinfo = pathinfo($basename);
                $basename = $pathinfo['filename'] . '-' . uniqid('') . '.' . $pathinfo['extension'];
            }
            $storedFileName = $file->storeAs($directory, $basename, 'public');
            $media = Media::create([
                'model_id' => $request->get('model_id'),
                'model_type' => $modelType,
                'entity' => $entity,
                'user_id' => Auth::guard('admin')->id(),
                'path' => 'storage'.DIRECTORY_SEPARATOR.$storedFileName,
            ]);
            array_push($items, $media->fresh());
        }
        // если режим single file то удалять текущий файл
        if (!$request->get('multiple')) {
            $dropIds = Media::where('model_id', $request->get('model_id'))
                ->where('model_type', $modelType)
                ->where('entity', $entity)
                ->whereNotIn('id', Arr::pluck($items, 'id'))
                ->orderBy('order', 'ASC')
                ->get('id')
                ->pluck('id')
                ->toArray();

            if ($dropIds) {
                $destroyRequest = new Request(['id' => $dropIds]);
                $this->deleteMedia($destroyRequest);
            }
        }
        return $this->response([
            MSG => 'Success upload file',
            DATA => [
                'items' => $items,
            ]
        ]);
    }

    /**
     * Загрузка файла по ссылке
     *
     * @param Request $request
     * @return array
     */
    public function postMediaUploadFromUrl(Request $request)
    {
        $model = (new Media)->uploadFromUrl($request->all());
        return $this->response([
            MSG => 'Files success upload',
            DATA => $model,
        ]);
    }

    /**
     * Задать media для модели
     *
     * @param Request $request
     * @return array
     */
    public function postSetMedia(Request $request)
    {
        $this->validate($request, [
            'model_id' => ['required', 'numeric', 'min:1'],
            'model_type' => ['required'],
            'entity' => ['sometimes', 'nullable'],
            'media' => ['required'],
            'multiple' => ['required', 'boolean'],
        ]);
        // id выбранных файлов
        $ids = is_array($request->input('media')) ? $request->input('media') : [$request->input('media')];
        $modelType = 'App\\Models\\' . ucfirst($request->get('model_type'));
        $entity = $request->get('entity') ?: Media::ENTITY_DEFAULT;
        // выбранные медиа файлы
        $mediaItems = Media::whereIn('id', $ids)->get();
        if (!$mediaItems) {
            throw new ExceptionWithMessage("unknown media model(s)");
        }
        $copyModels = [];
        // проверить наличие папки назначения и создать если ее нет
        $directory = 'storage'.DIRECTORY_SEPARATOR.($request->get('path') ?: $request->get('model_type'));
        if (!is_dir($directory)) {
            File::makeDirectory($directory);
        }
        $mediaItems->map(function($media) use($request, $modelType, $entity, &$copyModels) {
            // получить параметры файла
            $pathinfo = pathinfo(strtok($media->getOriginal('path'), '?'));
            // имя текущего файла
            $filename = $pathinfo['filename'];
            // обновить хеш файла или добавить если его нет
            $filename = preg_replace('/-[0-9a-f]{13,}/i', '-'.uniqid(), $filename, -1, $count);
            if (!$count) {
                $filename .= '-'.uniqid();
            }
            // расширение текущего файла
            $extension = $pathinfo['extension'];
            // путь назначения нового файла
            $destination = 'storage'.DIRECTORY_SEPARATOR.($request->get('path') ?: $request->get('model_type')).DIRECTORY_SEPARATOR.$filename.'.'.$extension;
            // скопировать файл в новую папку если он есть
            if (file_exists($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['basename'])) {
                File::copy($pathinfo['dirname'].DIRECTORY_SEPARATOR.$pathinfo['basename'], $destination);
            }
            // дублировать модель
            $copy = $media->replicate();
            $copy->model_id = $request->get('model_id');
            $copy->model_type = $modelType;
            $copy->entity = $entity;
            $copy->path = $destination;
            $copy->created_at = date('Y-m-d H:i:s');
            $copy->updated_at = date('Y-m-d H:i:s');
            // очистить все динамические поля
            $copy->setAppends([]);
            array_push($copyModels, $copy->getAttributes());
        });
        // массовая вставка моделей
        Media::insert($copyModels);
        // получить последние записи
        $last = Media::latest()->take(count($copyModels))->get();
        // если устанавливается файл в одиночном режиме,
        // то удалить текущий медиа файл
        if (!$request->get('multiple')) {
            // удаление старых файлов
            $dropIds = Media::where(function($query) use ($request, $modelType, $entity) {
                $query->where('model_id', $request->get('model_id'))
                    ->where('model_type', $modelType)
                    ->where('entity', $entity);
            })
                ->whereNotIn('id', $last->pluck('id')->toArray())
                ->get()
                ->pluck('id')
                ->toArray();
            if (count($dropIds)) {
                $destroyRequest = new Request(['id' => $dropIds]);
                $this->deleteMedia($destroyRequest);
            }
        }

        return $this->response([
            MSG => 'Media success set to model',
            DATA => [
                'media' => $last,
            ],
        ]);
    }

    /**
     * Редактирование медиа файла (crop, resize, revert, rotate)
     *
     * @param Request $request
     * @return array
     */
    public function postMediaEdit(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', 'min:1'],
        ]);
        $media = Media::findOrFail($request->get('id'));
        // чистый URL файла (без query параметров)
        $image = Image::make(strtok($media->getOriginal('path'), '?'));
        // поворот
        if ($request->get('rotate') !== 0) {
            $image->rotate(-1 * $request->get('rotate'));
        }
        // обрезать
        $image->crop((int)$request->get('width'), (int)$request->get('height'), (int)$request->get('x'), (int)$request->get('y'));
        // повернуть по горизонтали
        if ($request->get('scaleX') === -1) {
            $image->flip('h');
        }
        // повернуть по вертикали
        if ($request->get('scaleY') === -1) {
            $image->flip('v');
        }
        // изменение размера
        if ($request->filled('widen') && $request->filled('heighten')) {
            $w = $request->get('widen');
            $h = $request->get('heighten');

            $width = $request->get('proportion') && $h > $w ? null : $w;
            $height = $request->get('proportion') && $w > $h ? null : $h;
            $image->resize($width, $height, function ($constraint) use ($request) {
                if ($request->get('proportion')) {
                    $constraint->aspectRatio();
                }
            });
        }
        $image->save();
        $image->destroy();
        // обновить хеш изображения
        $media->path = $this->setMediaHashname($media->getOriginal('path'));
        $media->save();

        return $this->response([
            MSG => 'Media success save',
            DATA => [
                'media' => $media,
            ]
        ]);
    }

    private function setMediaHashname(string $path) :string
    {
        $query = parse_url($path, PHP_URL_QUERY);
        parse_str($query, $params);
        $params['v'] = uniqid();
        return strtok($path, '?') . '?' . http_build_query($params);
    }

    /**
     * Изменить мета данные медиа файла
     *
     * @param Request $request
     * @return array
     */
    public function putMediaMeta(Request $request)
    {
        $this->validate($request, [
            'id' => ['required'],
            'basename' => ['sometimes'],
            'title' => ['sometimes'],
            'description' => ['sometimes'],
        ]);
        $media = Media::findOrFail($request->get('id'));
        if ($request->get('title')) {
            $media->title = $request->get('title');
        }
        if ($request->get('description')) {
            $media->description = $request->get('description');
        }
        // переименование файла
        $currentPath = $media->getOriginal('path');
        if ($request->get('basename') && $request->get('basename') != $currentPath) {
            $currentPathinfo = pathinfo($currentPath);
            $requestPathinfo = pathinfo($request->get('basename'));
            $destination = $currentPathinfo['dirname'].DIRECTORY_SEPARATOR.$requestPathinfo['basename'];
            if (File::move($currentPath, $destination)) {
                $media->path = $destination;
            }
        }
        $media->save();
        return $this->response([MSG => 'Meta data success update', DATA => $media]);
    }

    /**
     * Изменить сортировку галлереи
     *
     * @param Request $request
     * @return array
     */
    // public function putGalleryOrder(Request $request)
    // {
    //     $this->validate($request, [
    //         'position' => ['required'],
    //     ]);
    //     Media::updateValues($request->input('position'));
    //     return $this->response([MSG => 'Gallery order success update']);
    // }

    /**
     * Удаление media обьекта
     *
     * @param Request $request
     * @return array
     */
    public function deleteMedia(Request $request)
    {
        $fileDeleted = (new Media)->deleteMedia($request->all());
        return $this->response([MSG => "Success delete {$fileDeleted} files"]);
    }
}
