<?php


namespace App\Dtes;

class File
{

    public static function rmdir($dir)
    {
        // List the contents of the directory table
        $dir_content = scandir ($dir);
        // Is it a directory?
        if ($dir_content!==false) {
            // For each directory entry
            foreach ($dir_content as &$entry) {
                // Unix symbolic shortcuts, we go
                if (!in_array ($entry, array ('.','..'))) {
                    // We find the path from the beginning
                    $entry = $dir.DIRECTORY_SEPARATOR. $entry;
                    // This entry is not an issue: it clears
                    if (!is_dir($entry)) {
                        unlink ($entry);
                    } else { // This entry is a folder, it again on this issue
                        self::rmdir($entry);
                    }
                }
            }
        }
        // It has erased all entries in the folder, we can now erase
        rmdir ($dir);
    }

    public static function mimetype($file)
    {
        if (!function_exists('finfo_open'))
            return false;
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $file);
        finfo_close($finfo);
        return $mimetype;
    }

    public static function compress($file, $options = [])
    {
        // definir opciones por defecto
        $options = array_merge([
            'format' => 'gz',
            'delete' => false,
            'download' => true,
            'commands' => [
                'gz' => 'gzip --keep :in',
                'tar.gz' => 'tar czf :in.tar.gz :in',
                'tar' => 'tar cf :in.tar :in',
                'bz2' => 'bzip2 --keep :in',
                'tar.bz2' => 'tar cjf :in.tar.bz2 :in',
                'zip' => 'zip -r :in.zip :in',
            ],
        ], $options);
        // si el archivo no se puede leer se entrega =false
        if (!is_readable($file)) {
            \App\Dtes\Log::write(Estado::COMPRESS_ERROR_READ, Estado::get(Estado::COMPRESS_ERROR_READ));
            return false;
        }
        // si es formato gz y es directorio se cambia a tgz
        if (is_dir($file)) {
            if ($options['format']=='gz') $options['format'] = 'tar.gz';
            else if ($options['format']=='bz2') $options['format'] = 'tar.bz2';
        }
        // obtener directorio que contiene al archivo/directorio y el nombre de este
        $filepath = $file;
        $dir = dirname($file);
        $file = basename($file);
        $file_compressed = $file.'.'.$options['format'];
        // empaquetar/comprimir directorio/archivo
        if ($options['format']=='zip') {
            // crear archivo zip
            $zip = new \ZipArchive();
            if ($zip->open($dir.DIRECTORY_SEPARATOR.$file.'.zip', \ZipArchive::CREATE)!==true) {
                \App\Dtes\Log::write(Estado::COMPRESS_ERROR_ZIP, Estado::get(Estado::COMPRESS_ERROR_ZIP));
                return false;
            }
            // agregar un único archivo al zip
            if (!is_dir($filepath)) {
                $zip->addFile($filepath, $file);
            }
            // agregar directorio al zip
            else if (is_dir($filepath)) {
                $Iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filepath));
                foreach ($Iterator as $f) {
                    if (!$f->isDir()) {
                        $path = $f->getPath().DIRECTORY_SEPARATOR.$f->getFilename();
                        $zip->addFile($path, str_replace($filepath, '', $file.DIRECTORY_SEPARATOR.$path));
                    }
                }
            }
            // escribir en el sistema de archivos y cerrar archivo
            file_put_contents($dir.DIRECTORY_SEPARATOR.$file_compressed, $zip->getStream(md5($filepath)));
            $zip->close();
        } else {
            exec('cd '.$dir.' && '.str_replace(':in', $file, $options['commands'][$options['format']]));
        }
        // enviar archivo
        if ($options['download']) {
            ob_clean();
            header ('Content-Disposition: attachment; filename='.$file_compressed);
            $mimetype = self::mimetype($dir.DIRECTORY_SEPARATOR.$file_compressed);
            if ($mimetype)
                header ('Content-Type: '.$mimetype);
            header ('Content-Length: '.filesize($dir.DIRECTORY_SEPARATOR.$file_compressed));
            readfile($dir.DIRECTORY_SEPARATOR.$file_compressed);
            unlink($dir.DIRECTORY_SEPARATOR.$file_compressed);
        }
        // borrar directorio o archivo que se está comprimiendo si así se ha
        // solicitado
        if ($options['delete']) {
            if (is_dir($filepath)) self::rmdir($filepath);
            else unlink($filepath);
        }
    }

}
