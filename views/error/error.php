<h1><?= ApineAppTranslator::translate('errors', 'error') . ' ' . $this->_params['code'];?></h1>
<h2><?= $this->_params['message'];?></h2>

<?php if (ApineConfig::get('runtime', 'mode') == 'development' && isset($this->_params['trace'])) { ?>
<pre>
<?= $this->_params['message'].' on '.$this->_params['file'].' ('.$this->_params['line'].")\n\n"; ?>
<?= $this->_params['trace']; ?>
</pre>
<?php }?>