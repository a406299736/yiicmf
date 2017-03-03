<?php

namespace Helper;

use Curl\Curl;

class Test
{
    const TEST_URL = 'http://127.0.0.1:8000/';
    const ERROR_URL = 'https://1.2.3.4/';

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
    }

    public function server($test, $request_method, $query_parameters = array(), $data = array())
    {
        $this->curl->setHeader('X-DEBUG-TEST', $test);
        $request_method = strtolower($request_method);
        if (is_array($data) && empty($data)) {
            $this->curl->$request_method(self::TEST_URL, $query_parameters);
        } else {
            $this->curl->$request_method(self::TEST_URL, $query_parameters, $data);
        }
        return $this->curl->response;
    }

    /*
     * When chaining requests, the method must be forced, otherwise a
     * previously forced method might be inherited.
     * Especially, POSTs must be configured to not perform post-redirect-get.
     */
    private function chained_request($request_method)
    {
        if ($request_method === 'POST') {
            $this->server('request_method', $request_method, array(), true);
        } else {
            $this->server('request_method', $request_method);
        }
        \PHPUnit_Framework_Assert::assertEquals($request_method, $this->curl->responseHeaders['X-REQUEST-METHOD']);
    }

    public function chain_requests($first, $second)
    {
        $this->chained_request($first);
        $this->chained_request($second);
    }
}

function create_png()
{
    // PNG image data, 1 x 1, 1-bit colormap, non-interlaced
    ob_start();
    imagepng(imagecreatefromstring(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7')));
    $raw_image = ob_get_contents();
    ob_end_clean();
    return $raw_image;
}

function create_tmp_file($data)
{
    $tmp_file = tmpfile();
    fwrite($tmp_file, $data);
    rewind($tmp_file);
    return $tmp_file;
}

function get_tmp_file_path()
{
    // Return temporary file path without creating file.
    $tmp_file_path =
        rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) .
        DIRECTORY_SEPARATOR . 'php-curl-class.' . uniqid(rand(), true);
    return $tmp_file_path;
}

function get_png()
{
    $tmp_filename = tempnam('/tmp', 'php-curl-class.');
    file_put_contents($tmp_filename, create_png());
    return $tmp_filename;
}

if (function_exists('finfo_open')) {
    function mime_type($file_path)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $mime_type;
    }
} else {
    function mime_type($file_path)
    {
        $mime_type = mime_content_type($file_path);
        return $mime_type;
    }
}

function upload_file_to_server($upload_file_path) {
    $upload_test = new Test();
    $upload_test->server('upload_response', 'POST', array(
        'image' => '@' . $upload_file_path,
    ));
    $uploaded_file_path = $upload_test->curl->response->file_path;

    // Ensure files are not the same path.
    assert(!($upload_file_path === $uploaded_file_path));

    // Ensure file uploaded successfully.
    assert(md5_file($upload_file_path) === $upload_test->curl->responseHeaders['ETag']);

    return $uploaded_file_path;
}

function remove_file_from_server($uploaded_file_path) {
    $download_test = new Test();

    // Ensure file successfully removed.
    assert('true' === $download_test->server('upload_cleanup', 'POST', array(
        'file_path' => $uploaded_file_path,
    )));
    assert(file_exists($uploaded_file_path) === false);
}
