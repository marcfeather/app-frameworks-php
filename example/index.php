<?php

use PHPfox\Unity\App\Framework;

// require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../phpfox.php');

new Framework(function(Framework $app) {
	$configFile = __DIR__ . '/config.php';
	if (!file_exists($configFile)) {
		exit('Config file is missing.');
	}

	$config = require($configFile);
	call_user_func($config, $app);

	$app->route->on($app->env->get('base'), function(Framework $app) {
		$app->form->file('file', 'Select a File')
			->textarea('caption', 'Caption')
			->submit('Upload');

		$app->form->success(function(Framework $app) {
			$content = $app->request->get('content');
			$content = strip_tags($content);
			if (mb_strlen($content) > 160) {
				$app->error('Too long.');
			}

			$post = [
				'client_id' => $app->client->id(),
				'user_id' => $app->user->id,
				'time_stamp' => $app->date->timestamp(),
				'content' => mb_substr($content, 0, 160)
			];
			$id = $app->db->insert($post)->in('posts');
			$post['post_id'] = $id;

			return $app->js->query('#mb-new-posts')->prepend($app->page->render('_post.html', ['post' => $post]));
		});

		$posts = [];
		foreach ($app->db->select('*')->from('posts')->order('post_id DESC')->limit(10)->all() as $post) {
			$posts[] = $post;
		}

		print_r($posts);

		$app->page->title('Posts');

		return $app->page->render('index.html', [
			'action' => $app->url->make($app->env->get('base')),
			'form' => $app->form->make(),
			'posts' => $posts,
			'assets' => $app->env->get('url')
		]);
	});
});