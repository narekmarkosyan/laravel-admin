<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Editor extends Field
{
    protected static $js = [
        'vendor/laravel-admin/ckeditor/ckeditor.js',
    ];

    public function render()
    {
        $id = json_encode($this->id);

        $this->script = <<<SCRIPT
var editor = document.getElementById({$id});

if (editor && editor.tagName.toLowerCase() === 'textarea') {
    if (CKEDITOR.instances[editor.id]) {
        CKEDITOR.instances[editor.id].destroy(true);
    }

    CKEDITOR.replace(editor);
}
SCRIPT;

        return parent::render();
    }
}
