<?php

//required include files
require('../../../wp-blog-header.php');
require_once('../../../wp-config.php');
require_once('../../../wp-includes/wp-db.php');

function update_todo_items()
{
    // Get data from database or API.
    $api_url = 'https://jsonplaceholder.typicode.com/todos/';

    // Read JSON file
    //$json_data = file_get_contents($api_url);

    $json_data = '[
        {
          "userId": 1,
          "id": 1,
          "title": "delectus aut autem",
          "completed": false
        },
        {
          "userId": 1,
          "id": 2,
          "title": "quis ut nam facilis et officia qui",
          "completed": false
        },
        {
          "userId": 1,
          "id": 3,
          "title": "fugiat veniam minus",
          "completed": false
        },
        {
          "userId": 1,
          "id": 4,
          "title": "et porro tempora",
          "completed": true
        },
        {
          "userId": 1,
          "id": 5,
          "title": "laboriosam mollitia et enim quasi adipisci quia provident illum",
          "completed": false
        },
        {
          "userId": 1,
          "id": 6,
          "title": "qui ullam ratione quibusdam voluptatem quia omnis",
          "completed": false
        },
        {
          "userId": 1,
          "id": 7,
          "title": "illo expedita consequatur quia in",
          "completed": false
        },
        {
          "userId": 1,
          "id": 8,
          "title": "quo adipisci enim quam ut ab",
          "completed": true
        },
        {
          "userId": 1,
          "id": 9,
          "title": "molestiae perspiciatis ipsa",
          "completed": false
        },
        {
          "userId": 1,
          "id": 10,
          "title": "illo est ratione doloremque quia maiores aut",
          "completed": true
        }
      ]';

    // Decode JSON data into PHP array
    $todo_data = json_decode($json_data, true);

    foreach ($todo_data as $todo_item) {
        $args = [
            'post_type' => 'todo',
            'meta_query' => [
                [
                    'key' => 'remote_id',
                    'value' => $todo_item['id']
                ]
            ]
        ];

        $query = new WP_Query($args);
        $existing_post = false;

        if ($query->have_posts()) {
            $query->the_post();
            $existing_post = get_the_ID();

            echo "Will update post $existing_post<br>";

            $post_id = wp_update_post(
                [
                    'ID'                => $existing_post,
                    'post_title'        => $todo_item['title'],
                ]
            );
        } else {
            echo "Will create a new post<br>";
            $post_id = wp_insert_post(
                [
                    'comment_status'    => 'closed',
                    'ping_status'       => 'closed',
                    'post_author'       => 1,
                    'post_title'        => $todo_item['title'],
                    'post_status'       => 'publish',
                    'post_type'         => 'todo'
                ]
            );
        }

        update_post_meta($post_id, 'remote_id', $todo_item['id']);
        update_post_meta($post_id, 'remote_userid', $todo_item['userId']);
        update_post_meta($post_id, 'remote_completed', $todo_item['completed']);
    }


    return;
}

update_todo_items();
