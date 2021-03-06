<?php

class C02_Station_MediaCest extends CestAbstract
{
    /**
     * @before setupComplete
     * @before login
     */
    public function editMedia(FunctionalTester $I)
    {
        $I->wantTo('Upload a song to a station.');

        $station_id = $this->test_station->getId();

        $test_song_orig = $this->settings[\App\Settings::BASE_DIR] . '/resources/error.mp3';
        $test_song = tempnam(sys_get_temp_dir(), 'azuracast');
        copy($test_song_orig, $test_song);

        $test_file = [
            'tmp_name' => $test_song,
            'name' => basename($test_song),
            'type' => 'audio/mpeg',
            'size' => filesize($test_song),
            'error' => \UPLOAD_ERR_OK,
        ];

        $I->sendPOST('/api/station/' . $station_id . '/files/upload', [
            'file' => '',
            'csrf' => '', // CSRF disabled in testing.
            'flowIdentifier' => 'uploadtest',
            'flowChunkNumber' => 1,
            'flowCurrentChunkSize' => filesize($test_song),
            'flowFilename' => 'error.mp3',
            'flowTotalSize' => filesize($test_song),
            'flowTotalChunks' => 1,
        ], [
            'file_data' => $test_file,
        ]);

        $I->seeResponseContainsJson([
            'success' => true,
        ]);

        $I->sendGET('/api/station/' . $station_id . '/files/list');

        $I->seeResponseContainsJson([
            'media_name' => 'AzuraCast.com - AzuraCast is Live!',
        ]);

        $I->amOnPage('/station/' . $station_id . '/files');

        $I->see('Music Files');
    }
}
