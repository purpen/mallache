<?php

namespace App\Http;

trait ApiHelper
{
    /**
     * 响应正确信息
     * @param string $message 响应提示信息
     * @param int $code 响应状态码
     * @param array $data 响应数据
     * @param array $meta 对meta的补充
     *
     * @return array
     */
    public function apiSuccess($message = 'Success', $status_code = 200, $data = null, $meta = array())
    {
        $result['meta'] = array(
            'message' => $message,
            'status_code' => $status_code
        );

        if (!empty($meta)) {
            $result['meta'] = array_merge($result['meta'], $meta);
        }

        if ($data !== null) {
            $result['data'] = $data;
        }

        return $result;
    }

    /**
     * 保持返回json格式一致
     * @param string $message 响应提示信息
     * @param int $code 响应状态码
     *
     * @return array
     */
    public function apiMeta($message = 'Success', $status_code = 200, $meta = array())
    {
        $result = [
            'message' => $message,
            'status_code' => $status_code,
        ];

        if (!empty($meta)) {
            $result = array_merge($result, $meta);
        }

        return $result;
    }

    /**
     * 响应错误信息
     * @param string $message 响应提示信息
     * @param int $code 响应状态码
     *
     * @return array
     */
    public function apiError($message = 'Error', $status_code = 400)
    {
        $result['meta'] = array(
            'message' => $message,
            'status_code' => $status_code
        );

        return $result;
    }

}