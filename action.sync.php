<?php
if (!cmsms()) exit;

if ($remote_url = $this->GetPreference('remote_gallery', false)) {
    $json = file_get_contents($remote_url);

    if ($json !== false) {
        $medias = json_decode($json);

        if (isset($medias->results)) {
            $elements = 0;
            $update = 0;
            $insert = 0;
            // var_dump($medias->results);
            foreach ($medias->results as $media) {
                /** @var $media object */
                $elements++;
                $save_params = array('frontend' => true, 'no_time_increment' => true);
                // var_dump($media);
                if (!($local_media = MCFile::retrieveByRemoteId($media->id))) {
                    $local_media = new MCFile($media->collection_id);
                    $local_media->setRemoteId($media->id);
                    $save_params['force_insert'] = true;
                }

                if ($local_media->getUpdatedAt() != $media->updated_at) {
                    if (isset($save_params['force_insert'])) {
                        $insert++;
                    } else {
                        $update++;
                    }
                    $local_media->setCreatedAt($media->created_at);
                    $local_media->setUpdatedAt($media->updated_at);
                    $local_media->setPosition($media->position);
                    $local_media->setTitle($media->title);

                    $local_media->uploadFromUrl($media->filename_url, $media->original_filename, $media->filename);
                    $local_media->save($save_params);
                }
            }
            echo 'Synchronisation success for [' . $elements . '] elements: Updated: [' . $update . '] - Inserted: [' . $insert . ']';
        }
    } else {
        echo 'Unable to connect to the url: ' . $remote_url;
    }
} else {
    echo "Remote gallery undefined";
}