<?php
/*
 * Copyright 2018 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mibew\Mibew\Plugin\AutoReply;

use Mibew\Database;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Thread;

/**
 * Provides an ability to automatically reply a visitor in a queue.
 */
class Plugin extends \Mibew\Plugin\AbstractPlugin implements \Mibew\Plugin\PluginInterface
{
    protected $initialized = true;

    // Time to wait before reply
    protected $wait_time = 300;

    /**
     * Class constructor.
     *
     * @param array $config List of the plugin config.
     *
     */
    public function __construct($config)
    {
        if (isset($config['wait_time']) && ((int)$config['wait_time'] > 0)) {
            $this->wait_time = (int)$config['wait_time'];
        }
    }

    /**
     * Defines necessary event listener.
     */
    public function run()
    {
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->attachListener('threadUpdate', $this, 'autoReply');
    }

    /**
     * Returns verision of the plugin.
     *
     * @return string Plugin's version.
     */
    public static function getVersion()
    {
        return '0.1.1';
    }

    /**
     * Automatically sends a system message to a visitor in a chat in queue after some time
     * of awaiting.
     *
     * @param array $args Event data
     */
    public function autoReply($args)
    {
        // Get the thread
        $thread = $args['thread'];
        // Get the old thread
        $original_thread = $args['original_thread'];

        // Check whether a visitor is waiting in the queue
        if ($thread->state == Thread::STATE_QUEUE) {
            // Check whether a visitor is in the queue for too long
            if (time() - $thread->modified > $this->wait_time) {
                // Check the auto reply is already sent
                $result = Database::getInstance()->query(
                    "SELECT COUNT(*) AS replied FROM {mibew_autoreply} WHERE threadid = :threadid",
                    array(':threadid' => $thread->id),
                    array('return_rows' => Database::RETURN_ONE_ROW)
                );

                if (!$result || !isset($result['replied']) || !($result['replied'] > 0)) {
                    // Send auto reply
                    $thread->postMessage(
                        Thread::KIND_INFO,
                        getlocal(
                            'All our operators are currently busy, please hold. Sorry for keeping you waiting.',
                            null,
                            $thread->locale,
                            false
                        )
                    );

                    // Mark thread as replied in the database
                    Database::getInstance()->query(
                        'INSERT INTO {mibew_autoreply} (threadid) VALUES (:threadid)',
                        array(':threadid' => $thread->id)
                    );
                }
            }
        }
        // Check whether a visitor is not in the queue anymore
        elseif ($thread->state != $original_thread->state) {
            // Remove the garbage from the database
            Database::getInstance()->query(
                'DELETE FROM {mibew_autoreply} WHERE threadid = :threadid',
                array(':threadid' => $thread->id)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function install()
    {
        // Initialize localization constant
        getlocal('All our operators are currently busy, please hold. Sorry for keeping you waiting.');

        return Database::getInstance()->query(
            'CREATE TABLE {mibew_autoreply} ( '
                . 'threadid INT NOT NULL PRIMARY KEY'
            . ') charset utf8 ENGINE=InnoDb'
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function uninstall()
    {
        return Database::getInstance()->query('DROP TABLE {mibew_autoreply}');
    }
}
