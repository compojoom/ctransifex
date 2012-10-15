<?php
/**
 * No matter if you like it or not - all folders need to have an index.html file in order for your
 * extension to be listed on the JED. Often one forget to add the index.html file to the folders...
 * This simple class will add an index.html file to each folder of your extension that needs to have an
 * index.html class. You can now stop putting those index.html files in your repository...
 *
 * @author Daniel Dimitrov - compojoom.com
 * @license GNU/GPL
 */

class indexhtmlTask extends Task
{

    public function setDir($dir)
    {
        $this->dir = $dir;
    }


    /**
     * The init method: Do init steps.
     */
    public function init()
    {
// nothing to do here
    }

    /**
     * The main entry point method.
     */
    public function main()
    {

        $path = $this->dir;

        $system_folders = array(
//            'language',
//            'language/.*'
        );

        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach($objects as $name => $object){
            if($object->isDir()) {
                if (!preg_match('#/('.implode('|', $system_folders).')$#i', str_replace('\\', '/', $name))) {
                    file_put_contents($name.'/index.html', '');
                }
            }
        }

    }
}