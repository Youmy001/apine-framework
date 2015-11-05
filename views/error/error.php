<h1><?= ApineAppTranslator::translate('errors', 'error') . ' ' . $this->_params->get_item('code');?></h1>
<h2><?= $this->_params->get_item('message');?></h2>

<?php if (Config::get('runtime', 'mode') == 'development' && !is_null($this->_params->get_item('trace'))) { ?>
<pre>
<?= $this->_params->get_item('message').' on '.$this->_params->get_item('file').' ('.$this->_params->get_item('line').")\n\n"; ?>
<?= $this->_params->get_item('trace'); ?>
</pre>
<?php }?>