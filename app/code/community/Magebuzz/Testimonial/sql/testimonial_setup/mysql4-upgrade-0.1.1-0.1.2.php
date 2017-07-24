<?php
/**
 * @category  Magebuzz
 * @package   Magebuzz_Testimonial
 * @version   0.1.5
 * @copyright Copyright (c) 2012-2015 http://www.magebuzz.com
 * @license   http://www.magebuzz.com/terms-conditions/
 */

$installer = $this;
$installer->startSetup();
	$installer->run("
		ALTER TABLE mg_simple_testimonial ADD avatar_name varchar(255) NULL default '' after email;
		ALTER TABLE mg_simple_testimonial ADD avatar_path varchar(255) NULL default '' after avatar_name;
	");
$installer->endSetup(); 