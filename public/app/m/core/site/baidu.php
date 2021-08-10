<?php
// 千千音乐

namespace site;

class baidu
{
    
    public static function search($query, $page)
    {
        if (empty($query)) {
            return;
        }
        $radio_search_url = [
            'method'         => 'GET',
            'url'            => 'http://musicapi.qianqian.com/v1/restserver/ting',
            'referer'        => 'http://music.taihe.com/',
            'proxy'          => false,
            'body'           => [
                'method'    => 'baidu.ting.search.common',
                'query'     => $query,
                'format'    => 'json',
                'page_no'   => $page,
                'page_size' => 10
            ]
        ];
        $radio_result = mc_curl($radio_search_url);
        if (empty($radio_result)) {
            return;
        }
        $radio_songid = [];
        $radio_data = json_decode($radio_result, true);
        if (empty($radio_data['song_list'])) {
            return;
        }
        foreach ($radio_data['song_list'] as $val) {
            $radio_songid[] = $val['song_id'];
        }
        return self::getSong($radio_songid, true);
    }

    public static function getSong($songid, $multi = false)
    {
        if (empty($songid)) {
            return;
        }
        if ($multi) {
            if (!is_array($songid)) {
                return;
            }
            $songid = implode(',', $songid);
        }
        $radio_song_url = [
            'method'        => 'GET',
            'url'           => 'http://music.taihe.com/data/music/links',
            'referer'       => 'music.taihe.com/song/' . $songid,
            'proxy'         => false,
            'body'          => [
                'songIds'   => $songid
            ]
        ];
        $radio_result = mc_curl($radio_song_url);
        if (empty($radio_result)) {
            return;
        }
        $radio_songs = [];
        $radio_json             = json_decode($radio_result, true);
        $radio_data             = $radio_json['data']['songList'];
        if (!empty($radio_data)) {
            foreach ($radio_data as $value) {
                $radio_song_id  = $value['songId'];
                if ($value['lrcLink']) {
                    $radio_lrc  = mc_curl(['url'=>$value['lrcLink']]);
                }
                $radio_songs[]  = [
                    'type'   => 'baidu',
                    'link'   => 'http://music.taihe.com/song/' . $radio_song_id,
                    'songid' => $radio_song_id,
                    'title'  => $value['songName'],
                    'author' => $value['artistName'],
                    'lrc'    => $radio_lrc,
                    'url'    => str_replace(
                        [
                            'yinyueshiting.baidu.com',
                            'zhangmenshiting.baidu.com',
                            'zhangmenshiting.qianqian.com'
                        ],
                        'gss0.bdstatic.com/y0s1hSulBw92lNKgpU_Z2jR7b2w6buu',
                        $value['songLink']
                    ),
                    'pic'    => $value['songPicBig']
                ];
            }
            return $radio_songs;
        }else{
            return;
        }
    }

    private static function getLyric($songid)
    {
        $radio_lrc_url = [
            'method'        => 'GET',
            'url'           => 'http://musicapi.qianqian.com/v1/restserver/ting',
            'referer'       => 'http://music.taihe.com/song/' . $songid,
            'proxy'         => false,
            'body'          => [
                'method' => 'baidu.ting.song.lry',
                'songid' => $songid,
                'format' => 'json'
            ]
        ];
        $radio_result = mc_curl($radio_lrc_url);
        $arr = json_decode($radio_result, true);
        return isset($arr['lrcContent']) ? $arr['lrcContent'] : null;
    }
}