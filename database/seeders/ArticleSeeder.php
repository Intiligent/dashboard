<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $pages = [
            [
                'title' => 'Service',
                'slug' => 'service',
                'lang_id' => 1,
                'text' => 'In todays digital age, effective content management is crucial for businesses and organizations of all sizes. Our Content Management System (CMS) Platform Development service offers a comprehensive solution for efficiently creating, organizing, and managing digital content across websites, applications, and various digital platforms. Whether youre a small business, a non-profit organization, or an enterprise, our CMS platform empowers you to take control of your online presence and streamline content workflows.',
            ],
            [
                'title' => 'About us',
                'slug' => 'about',
                'lang_id' => 1,
                'text' => 'Our team of experienced developers and designers is dedicated to creating a tailored CMS platform that meets your unique content management needs. We prioritize user-friendliness, scalability, and performance, ensuring that your organization can effectively harness the power of digital content.',
            ],
            [
                'title' => 'Payment and shiping',
                'slug' => 'delivery',
                'lang_id' => 1,
                'text' => 'Efficient payment processing and seamless shipping are essential components of any successful e-commerce or online business. Our Payment and Shipping Integration service offers a comprehensive solution to handle these critical aspects of your business, ensuring a smooth and convenient experience for both you and your customers. By integrating secure payment gateways and optimizing shipping processes, we enable you to focus on growing your business while delivering exceptional service.',
            ],
            [
                'title' => 'Contacts',
                'slug' => 'contact',
                'lang_id' => 1,
                'text' => 'We value your feedback, questions, and inquiries. Our contact page is the gateway to connect with us, whether youre a valued customer, a potential partner, or simply curious about what we do. Feel free to reach out through the various contact options provided below, and well be delighted to assist you.',
            ],
        ];

        foreach ($pages as $article) {
            Article::firstOrNew(Arr::only($article, ['title', 'slug']), $article)->save();
        }
    }
}
