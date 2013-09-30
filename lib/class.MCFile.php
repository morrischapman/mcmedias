<?php
    /*
      A file belong to a collection
    */
    class MCFile
    {
        protected $id;
        protected $collection_id;

        protected $position;
        protected $title;

        protected $filename;
        protected $original_filename;

        protected $created_at;
        protected $updated_at;

        protected $last_error;

        const DB_NAME = 'module_MCMedias_files';

        public function __construct($collection_id = '')
        {
            $this->collection_id = $collection_id;
        }

        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function getCollectionId()
        {
            return $this->collection_id;
        }

        public function setTitle($value)
        {
            $this->title = $value;
        }

        public function setPosition($position)
        {
            $this->position = (int)$position;
        }

        public function setCreatedAt($created_at)
        {
            $this->created_at = $created_at;
        }

        public function getUpdatedAt()
        {
            return $this->updated_at;
        }

        public function setUpdatedAt($updated_at)
        {
            $this->updated_at = $updated_at;
        }

        public function getOriginalFilename()
        {
            return $this->original_filename;
        }

        public function getFilename()
        {
            // return $this->id . '_' . $this->filename;
            return $this->filename;
        }

        public function getFilepath()
        {
            return self::getUploadPath($this->collection_id) . DIRECTORY_SEPARATOR . $this->getFilename();
        }

        public function getRelativePath()
        {
            return $this->collection_id . DIRECTORY_SEPARATOR . $this->getFilename();
        }

        public function getUrl()
        {
            $config = cms_utils::get_config();

            return $config['uploads_url'] . $this->getRelativeUrl($this->collection_id);

        }

        protected function getRelativeUrl($url_path = NULL)
        {
            $url = DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . 'MCMedias';
            if (!is_null($url_path)) $url .= DIRECTORY_SEPARATOR . $url_path;
            $url .= DIRECTORY_SEPARATOR . $this->getFilename();

            return str_replace(DIRECTORY_SEPARATOR, '/', $url);
        }

        public static function getUploadPath($tail_path = NULL)
        {
            $MCMedias = cms_utils::get_module('MCMedias');
            $path     = $MCMedias->GetPreference('uploads_path', $MCMedias->config['uploads_path']) . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . 'MCMedias';

            if (!is_null($tail_path)) $path .= DIRECTORY_SEPARATOR . $tail_path;
            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true)) {
                    throw new Exception('Impossible to create the path ' . $path);
                }
            }

            return $path;
        }

        public function saveUploadedFile($fieldname)
        {

            $uploader = new qqFileUploader();

            $path = self::getUploadPath($this->collection_id);

            if (is_null($path)) {
                return json_encode(array('success' => false, 'error' => 'Impossible to create path for ' . $this->collection_id . ' with error ' . $this->last_error, 'preventRetry' => true));
            }

            // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
            $result = $uploader->handleUpload($path, self::cleanFilename($_FILES[$fieldname]['name']));

            if (isset($result['success'])) {
                $this->original_filename = $uploader->getName();
                $this->filename          = $uploader->getUploadName();
                // $this->filename = self::cleanFilename($_FILES[$fieldname]['name']);

                $this->save();

                // move_uploaded_file($_FILES[$fieldname]['tmp_name'], $this->getFilepath());

                // return true;
            }

            // return json_encode(array('error' => 'collection_id ' . $this->collection_id));

            // to pass data through iframe you will need to encode all html tags
            return htmlspecialchars(json_encode($result), ENT_NOQUOTES);

            /******************************************/
        }

        public function uploadFromLocal($file, $move = true)
        {
            if (is_file($file)) {
                $path = self::getUploadPath($this->collection_id);

                $filename = end(explode(DIRECTORY_SEPARATOR, $file));

                $this->original_filename = $filename;
                $this->filename          = $this->getNextAvailableFilename(self::cleanFilename($filename));

                if ($move) {
                    // var_dump($this->filename);
                    // var_dump($this->original_filename);
                    rename($file, $path . DIRECTORY_SEPARATOR . $this->filename);
                } else {
                    copy($file, $path . DIRECTORY_SEPARATOR . $this->filename);
                }

            }
        }

        public function uploadFromUrl($url, $original_filename = '', $filename = '')
        {

            $local_path = self::getUploadPath($this->collection_id);

            if ($filename != '') {
                $this->filename          = $filename;
                $this->original_filename = $original_filename;

                $image = file_get_contents($url);
                if ($image !== false) {
                    file_put_contents($local_path . DIRECTORY_SEPARATOR . $filename, $image);
                }
            }
        }

        public function downloadRemoteFile($url)
        {
            $path = self::getUploadPath($this->collection_id);

            copy($url, $path . DIRECTORY_SEPARATOR . $this->filename);
        }

        public function getNextAvailableFilename($filename, $prefix = NULL)
        {
            $local_path = self::getUploadPath($this->collection_id);

            if (is_file($local_path . DIRECTORY_SEPARATOR . $prefix . $filename)) {
                if (is_null($prefix)) {
                    $prefix = 1;
                } else {
                    $prefix++;
                }

                return $this->getNextAvailableFilename($filename, $prefix);
            } else {
                return $prefix . $filename;
            }

        }

        public static function getSystemSizeLimit()
        {
            $maxsize = ini_get('post_max_size');
            $base    = 1;
            if (strpos($maxsize, 'M')) {
                $base    = $base * 1024 * 1024;
                $maxsize = str_replace('M', '', $maxsize);
            }

            return $base * $maxsize;
        }

        public static function cleanFilename($filename)
        {
            $result = strtolower($filename);
            // Remove accents
            $result = strtr($result, "�����������������������������������������������������", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");

            // Soft way
            $result = str_replace("#", "No.", $result);
            $result = str_replace("$", "Dollar", $result);
            $result = str_replace("%", "Percent", $result);
            $result = str_replace("^", " ", $result);
            $result = str_replace("&", "and", $result);
            $result = str_replace("*", " ", $result);
            $result = str_replace("?", " ", $result);
            $result = str_replace(",", " ", $result);

            // strip all non word chars
            //$result = preg_replace('/\W/', ' ', $result); // HARD WAY...

            // replace all white space sections with a dash
            $result = preg_replace('/\ +/', '-', $result);
            // trim dashes
            $result = preg_replace('/\-$/', '', $result);
            $result = preg_replace('/^\-/', '', $result);

            return $result;
        }

        // FILE PROCESSING

        public function getFileSize($readable = false)
        {
            $file = $this->getFilepath();
            if (is_file($file)) {
                $size = filesize($file);

                if ($readable) {
                    return self::size_readable($size);
                } else {
                    return $size;
                }
            } else {
                return NULL;
            }
        }

        public static function size_readable($size, $retstring = NULL)
        {
            $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

            if ($retstring === NULL) {
                $retstring = '%01.2f %s';
            }

            $lastsizestring = end($sizes);

            foreach ($sizes as $sizestring) {
                if ($size < 1024) {
                    break;
                }
                if ($sizestring != $lastsizestring) {
                    $size /= 1024;
                }
            }

            if ($sizestring == $sizes[0]) {
                $retstring = '%01d %s';
            } // Bytes aren't normally fractional

            return sprintf($retstring, $size, $sizestring);
        }

        public function getFileExtension()
        {
            $file = explode('.', $this->getFilename());
            if (count($file) > 1) {
                return end($file);
            } else {
                return NULL;
            }
        }

        public function getFileIcon($icon_type = 'small')
        {
            $file   = $this->getFilepath();
            $module = cms_utils::get_module('MCMedias');
            $icon   = $module->GetModuleURLPath() . '/images/icons/' . $icon_type . '/';

            switch (strtolower(self::getFileExtension($file))) {
                case 'pdf':
                    $icon .= 'pdf.gif';
                    break;
                case 'js':
                    $icon .= 'js.gif';
                    break;
                case 'eps':
                    $icon .= 'eps.gif';
                    break;
                case 'php':
                    $icon .= 'php.gif';
                    break;
                case 'rar':
                    $icon .= 'rar.gif';
                    break;
                case 'doc':
                    $icon .= 'doc.gif';
                    break;
                case 'docx':
                    $icon .= 'doc.gif';
                    break;
                case 'rtf':
                    $icon .= 'rtf.gif';
                    break;
                case 'gif':
                    $icon .= 'gif.gif';
                    break;
                case 'txt':
                    $icon .= 'txt.gif';
                    break;
                case 'xls':
                    $icon .= 'xls.gif';
                    break;
                case 'xlsx':
                    $icon .= 'xls.gif';
                    break;
                case 'ppt':
                    $icon .= 'ppt.gif';
                    break;
                case 'pps':
                    $icon .= 'ppt.gif';
                    break;
                case 'pptx':
                    $icon .= 'ppt.gif';
                    break;
                case 'jpg':
                    $icon .= 'jpg.gif';
                    break;
                case 'bmp':
                    $icon .= 'bmp.gif';
                    break;
                case 'html':
                    $icon .= 'html.gif';
                    break;
                case 'htm':
                    $icon .= 'html.gif';
                    break;
                case 'mov':
                    $icon .= 'mov.gif';
                    break;
                case 'zip':
                    $icon .= 'zip.gif';
                    break;
                default:
                    $icon .= 'def.gif';
                    break;
            }

            return $icon;
        }

        // IMAGE PROCESSING

        public static function generateThumbnailFilename($filename, $params = array())
        {

            $file = 'Thumbnail';

            $filename = str_replace('..', '', $filename);
            $filename = str_replace(' ', '', $filename);
            $filename = str_replace(';', '', $filename);
            $filename = str_replace(DIRECTORY_SEPARATOR, '_', $filename);
            $filename = str_replace('/', '_', $filename);

            if (isset($params['width'])) $file .= '_w' . (int)$params['width'];
            if (isset($params['height'])) $file .= '_h' . (int)$params['height'];
            if (isset($params['mode'])) $file .= '_m_' . (string)str_replace(DIRECTORY_SEPARATOR, '_', $params['mode']);

            $file .= '_' . $filename;

            return $file;
        }

        public static function generateThumbnail($origin, $destination, $params = array())
        {
            if (is_file($origin)) {
                if (!isset($params['mode'])) $params['mode'] = 'default';
                if (!isset($params['width'])) $params['width'] = '100';
                if (!isset($params['height'])) $params['height'] = '100';

                $image = self::resizeImage($origin, $params['mode'], $params['width'], $params['height']);

                if ($image) {
                    imagejpeg($image, $destination, 100);
                }
            }
        }

        protected static function resizeImage($image, $mode, $width, $height)
        {
            switch ($mode) {
                default:
                    // Default = Downscale + Preserve
                    return self::downscale($image, $width, $height, true);
            }

            // SHOULD ADD:
            // CROP: UPSCALE - POSITION - CROP
            // UPSCALE: IMAGE FILL SPACE
            // DOWNSCALE: IMAGE FIT SPACE (smaller are made bigger)
        }

        protected static function downscale($image, $width, $height, $preserve = false)
        {
            list($current_width, $current_height) = getimagesize($image);

            $new_image = self::createImage($image);

            $ratio_orig = $current_width / $current_height;

            if (($current_height > $height) || ($current_width > $width)) {
                // echo "Too big: $current_width / $width and $current_height / $height";
                // CHOOSE THE BEST RATIO
                if ($width / $height > $ratio_orig) {
                    $width = round($height * $ratio_orig);
                } else {
                    $height = round($width / $ratio_orig);
                }
                // echo "<br /> Resize to:  $width and $height";
            } elseif ($preserve) {
                // echo "Preserve: $current_width / $width and $current_height / $height";
                // Both are smaller or equal but we want to preserve size
                return $new_image;
            } else {
                // TO FIX: Should upscale
                return $new_image;
            }


            $blank_image = imagecreatetruecolor($width, $height);
            if (imagecopyresampled($blank_image, $new_image, 0, 0, 0, 0, $width, $height, $current_width, $current_height)) {
                return $blank_image;
            } else {
                // AN ERROR OCCURED
                return false;
            }
        }

        protected static function createImage($image)
        {
            switch (exif_imagetype($image)) {
                case 1:
                    $srcimg = imagecreatefromgif($image);
                    break;
                case 2:
                    $srcimg = imagecreatefromjpeg($image);
                    break;
                case 3:
                    $srcimg = imagecreatefrompng($image);
                    break;
                default:
                    return false;
            }

            return $srcimg;
        }

        // DATABASE

        public static function retrieveByPk($id)
        {
            return self::doSelectOne(array('where' => array('id' => (int)$id)));
        }

        public static function doSelectOne($params = array())
        {
            $params['limit'] = 1;
            $items           = self::doSelect($params);
            if ($items) {
                return current($items);
            } else {
                return NULL;
            }
        }

        public static function doSelect($params = array())
        {

            $query = 'SELECT * FROM ' . cms_db_prefix() . self::DB_NAME;

            $values = array();

            if (isset($params['where']) && is_array($params['where'])) {
                $fields = array();
                foreach ($params['where'] as $field => $value) {
                    $fields[] = $field . '= ?';
                    $values[] = $value;
                }
                $query .= ' WHERE ' . implode(' AND ', $fields);
            }

            if (isset($params['order_by'])) {
                $query .= ' ORDER BY ' . implode(', ', $params['order_by']);
            } else {
                $query .= ' ORDER BY position';
            }

            if (isset($params['limit'])) {
                $query .= ' LIMIT ' . (int)$params['limit'];
            }

            $db       = cms_utils::get_db();
            $dbresult = $db->Execute($query, $values);
            $items    = array();

            if ($dbresult && $dbresult->RecordCount() > 0) {
                while ($dbresult && $row = $dbresult->FetchRow()) {
                    $item = new MCFile();
                    $item->populate($row);
                    if (isset($params['with_id'])) {
                        $items[$row['id']] = $item;
                    } else {
                        $items[] = $item;
                    }
                }
            }

            return $items;
        }

        public function populate($row)
        {
            $this->id = $row['id'];

            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];

            $this->collection_id     = $row['collection_id'];
            $this->position          = $row['position'];
            $this->title             = $row['title'];
            $this->filename          = $row['filename'];
            $this->original_filename = $row['original_filename'];
        }

        public function toArray()
        {
            return array(
                'id'                => $this->id,
                'created_at'        => $this->created_at,
                'updated_at'        => $this->updated_at,
                'collection_id'     => $this->collection_id,
                'position'          => $this->position,
                'title'             => $this->title,
                'filename'          => $this->filename,
                'original_filename' => $this->original_filename,
                'filename_url'      => $this->getUrl()
            );
        }

        public static function itemsToArray(Array $list)
        {
            $array = array();
            foreach ($list as $item) {
                $array[$item->id] = $item->toArray();
            }

            return $array;
        }

        public function save($params = array())
        {
            if ($this->id && !isset($params['force_insert'])) {
                $this->update($params);
            } else {
                $this->insert($params);
            }

            return true;
        }

        protected function getNextPosition()
        {
            $db     = cms_utils::get_db();
            $query  = 'SELECT MAX(position) + 1 AS position FROM ' . cms_db_prefix() . self::DB_NAME . ' WHERE collection_id = ?';
            $result = $db->execute($query, array($this->collection_id));
            $row    = $result->FetchRow();

            return $row['position'] ? $row['position'] : 1;
        }

        protected function insert($params = array())
        {

            $db = cms_utils::get_db();

            if (!isset($this->position) || is_null($this->position)) {
                $this->position = $this->getNextPosition();
            }

            if (isset($params['frontend'])) {
                $userid = NULL;
            } else {
                $userid = get_userid();
            }

            // UPDATE
            if (isset($params['no_time_increment'])) {
                $updated_at = $this->updated_at;
            } else {
                $updated_at = time();
            }

            $query = 'INSERT INTO ' . cms_db_prefix() . self::DB_NAME . '
        SET 
          collection_id = ?,
          
          created_at = ?,
          created_by = ?,
          updated_at = ?,
          updated_by = ?,

          title = ?,
          position = ?,
          filename = ?,
          original_filename = ?';

            $values = array($this->collection_id,
                time(),
                $userid,
                $updated_at,
                $userid,

                $this->title,
                $this->position,
                $this->filename,
                $this->original_filename);

            if (isset($this->id) && !is_null($this->id) && isset($params['force_insert'])) {
                $query .= ', id = ?';
                $values[] = $this->id;
            }

            $db->Execute($query, $values);

            $this->id = $db->Insert_ID();

            return true;
        }

        protected function update($params = array())
        {
            $db = cms_utils::get_db();
            if (isset($params['frontend'])) {
                $userid = NULL;
            } else {
                $userid = get_userid();
            }
            $query = 'UPDATE ' . cms_db_prefix() . self::DB_NAME . '
        SET 
          updated_at = ?,
          updated_by = ?,

          title = ?,
          position = ?
        WHERE id = ?';

            // UPDATE
            if (isset($params['no_time_increment'])) {
                $updated_at = $this->updated_at;
            } else {
                $updated_at = time();
            }

            $db->Execute($query, array(
                $updated_at,
                $userid,
                $this->title,
                $this->position,
                $this->id
            ));

            return true;
        }

        public function delete()
        {
            if ($this->id) {
                // FIRST DELETE THE FILE
                $file = $this->getFilepath();

                if (is_file($file)) {
                    unlink($file);
                }

                $query = 'DELETE FROM ' . cms_db_prefix() . self::DB_NAME . ' WHERE id = ?';
                $db    = cms_utils::get_db();
                $db->Execute($query, array($this->id));
            }

            return true;
        }

        public static function SyncFromUrl($url, $delete = true)
        {
            // IMPORT
            $data = file_get_contents($url);
            if ($data !== false) {
                $result = json_decode($data, true);
                if (($result != NULL) && (isset($result['results']))) {
                    $remote_items = $result['results'];
                    $local_items  = self::doSelect(array('with_id' => true));

                    foreach ($remote_items as $id => $item) {
                        if (isset($local_items[$id])) {
                            if ($item['updated_at'] > $local_items[$id]->updated_at) {
                                // Update it
                                $local_items[$id]->populate($item);
                                $local_items[$id]->downloadRemoteFile($item['filename_url']);
                                $local_items[$id]->save(array('no_time_increment' => true));
                            }
                        } else {
                            // Create
                            $local_items[$id] = new self();
                            $local_items[$id]->populate($item);
                            $local_items[$id]->downloadRemoteFile($item['filename_url']);
                            $local_items[$id]->save(array('no_time_increment' => true, 'force_insert' => true));
                        }
                    }

                    // DELETE NOT EXISTANT
                    if ($delete) {
                        foreach ($local_items as $id => $item) {
                            if (!isset($remote_items[$id])) {
                                $item->delete();
                            }
                        }
                    }

                }
            }

            return false;
        }

    }