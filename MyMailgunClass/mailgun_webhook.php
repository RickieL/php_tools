<?php
require_once 'webhook.class.php';
$webhook = new webhook();
$webhook->post_data = $_POST;
$webhook->CommitEvent();