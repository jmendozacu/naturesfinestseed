<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("order", "delivery_signature", array("type"=>"varchar"));
$installer->addAttribute("quote", "delivery_signature", array("type"=>"varchar"));
$installer->endSetup();