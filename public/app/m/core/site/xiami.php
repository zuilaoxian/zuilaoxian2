<?php
// 虾米音乐

namespace site;

class xiami
{
    
    public static function search($query, $page)
    {
        if (empty($query)) {
            return;
        }
        $radio_search_url = [
            'method'         => 'GET',
            'url'            => 'https://api.xiami.com/web',
            'referer'        => 'https://m.xiami.com',
            'proxy'          => false,
            'body'           => [
                'key'        => $query,
                'v'          => '2.0',
                'app_key'    => '1',
                'r'          => 'search/songs',
                'page'       => $page,
                'limit'      => 10
            ],
            'user-agent'     => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1'
        ];
        $radio_result = mc_curl($radio_search_url);
        if (empty($radio_result)) {
            return;
        }
        $radio_data = json_decode($radio_result, true);
        if (empty($radio_data['data']) || empty($radio_data['data']['songs'])) {
            return;
        }
        $radio_songs = [];
        foreach ($radio_data['data']['songs'] as $value) {
            $radio_song_id       = $value['song_id'];
            $radio_lrc = '';
            if ($value['lyric']) {
                $radio_lrc  = mc_curl(['url'=>$value['lyric']]);
            }
            $radio_songs[]      = [
                'type'   => 'xiami',
                'link'   => 'https://www.xiami.com/song/' . $radio_song_id,
                'songid' => $radio_song_id,
                'title'  => $value['song_name'],
                'author' => $value['artist_name'],
                'lrc'    => strip_tags($radio_lrc),
                'url'    => $value['listen_file'],
                'pic'    => $value['album_logo'].'@!c-400-400'
            ];
        }
        return $radio_songs;
    }

    public static function getSong($songid, $self = false)
    {
        if (empty($songid)) {
            return;
        }
        $radio_song_url = [
            'method'         => 'GET',
            'url'            => 'https://api.xiami.com/web',
            'referer'        => 'https://m.xiami.com',
            'proxy'          => false,
            'body'           => [
                'v'          => '2.0',
                'app_key'    => '1',
                'id'          => $songid,
                'r'       => 'song/detail'
            ],
            'user-agent'     => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1'
        ];
        $radio_result = mc_curl($radio_song_url);
        if (empty($radio_result)) {
            return;
        }
        $radio_json                 = json_decode($radio_result, true);
        $radio_data                 = $radio_json['data'];
        if (!empty($radio_data)) {
            $radio_lrc          = '';
            $radio_song_id      = $radio_data['song']['song_id'];
            if ($radio_data['song']['lyric']) {
                $radio_lrc  = mc_curl(['url'=>$radio_data['song']['lyric']]);
            }
            $radio_songs      = [
                'type'   => 'xiami',
                'link'   => 'https://www.xiami.com/song/' . $radio_song_id,
                'songid' => $radio_song_id,
                'title'  => $radio_data['song']['song_name'],
                'author' => $radio_data['song']['singers'],
                'lrc'    => $radio_lrc,
                'url'    => $radio_data['song']['listen_file'],
                'pic'    => $radio_data['song']['logo'].'@!c-400-400'
            ];
            return $self ? $radio_songs : [$radio_songs];
        } else {
            if ($radio_json['message']) {
                $radio_songs        = [
                    'error' => $radio_json['message'],
                    'code' => 403
                ];
                return $radio_songs;
            }
            return;
        }
    }

    // 解密虾米 location
    private static function decode_xiami_location($location)
    {
        $location     = trim($location);
        $result       = [];
        $line         = intval($location[0]);
        $locLen       = strlen($location);
        $rows         = intval(($locLen - 1) / $line);
        $extra        = ($locLen - 1) % $line;
        $location     = substr($location, 1);
        for ($i       = 0; $i < $extra; ++$i) {
            $start    = ($rows + 1) * $i;
            $end      = ($rows + 1) * ($i + 1);
            $result[] = substr($location, $start, $end - $start);
        }
        for ($i       = 0; $i < $line - $extra; ++$i) {
            $start    = ($rows + 1) * $extra + ($rows * $i);
            $end      = ($rows + 1) * $extra + ($rows * $i) + $rows;
            $result[] = substr($location, $start, $end - $start);
        }
        $url          = '';
        for ($i       = 0; $i < $rows + 1; ++$i) {
            for ($j   = 0; $j < $line; ++$j) {
                if ($j >= count($result) || $i >= strlen($result[$j])) {
                    continue;
                }
                $url .= $result[$j][$i];
            }
        }
        $url          = urldecode($url);
        $url          = str_replace('^', '0', $url);
        return $url;
    }

}