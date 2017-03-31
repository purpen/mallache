define({ "api": [  {    "type": "get",    "url": "/city",    "title": "获取城市列表",    "version": "1.0.0",    "name": "city_list",    "group": "Common",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "oid",            "description": "<p>城市唯一id（0）</p>"          },          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "token",            "description": ""          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": " {\n   \"meta\": {\n        \"message\": \"Success\",\n        \"status_code\": 200\n    }\n }\n \"data\":[\n     {\n        \"oid\": 1,\n            \"name\": \"北京\",\n            \"pid\": 0,\n            \"sort\": 1\n      }\n    ]\n}",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/CommonController.php",    "groupTitle": "Common",    "sampleRequest": [      {        "url": "http://saas.me/city"      }    ]  },  {    "type": "get",    "url": "/upload/upToken",    "title": "生成上传图片upToken",    "version": "1.0.0",    "name": "upload_asset",    "group": "Upload",    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n   \"meta\": {\n     \"message\": \"Success\",\n     \"status_code\": 200\n   },\n   \"data\": {\n     \"upToken\": \"AWTEpwVNmNcVjsIL-vS1hOabJ0NgIfNDzvTbDb4i:csOk9LcG2lM0_3qvbDqmEUa87V8=:eyJjYWxsYmFja1VybCI6bnVsbCwiY2FsbGJhY2tGZXRjaEtleSI6MSwiY2FsbGJhY2tCb2R5IjoibmFtZT0kKGZuYW1lKSZzaXplPSQoZnNpemUpJm1pbWU9JChtaW1lVHlwZSkmd2lkdGg9JChpbWFnZUluZm8ud2lkdGgpJmhlaWdodD0kKGltYWdlSW5mby5oZWlnaHQpJnJhbmRvbT0kKHg6cmFuZG9tKSZ1c2VyX2lkPSQoeDp1c2VyX2lkKSZ0YXJnZXRfaWQ9JCh4OnRhcmdldF9pZCkiLCJzY29wZSI6bnVsbCwiZGVhZGxpbmUiOjE0OTA3NTUyMDh9\"\n     \"upload_url\": \"http://up-z1.qiniu.come\"\n    }\n}",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/UploadController.php",    "groupTitle": "Upload",    "sampleRequest": [      {        "url": "http://saas.me/upload/upToken"      }    ]  },  {    "type": "post",    "url": "/auth/login",    "title": "用户登录",    "version": "1.0.0",    "name": "user_login",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "account",            "description": "<p>用户账号</p>"          },          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "password",            "description": "<p>设置密码</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": " {\n   \"meta\": {\n     \"message\": \"登录成功！\",\n     \"status_code\": 200\n   }\n }\n \"data\": {\n    \"token\": \"token\",\n    \"status\": 0\n  }\n}",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/login"      }    ]  },  {    "type": "post",    "url": "/auth/logout",    "title": "退出登录",    "version": "1.0.0",    "name": "user_logout",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "token",            "description": ""          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n   \"meta\": {\n     \"message\": \"A token is required\",\n     \"status_code\": 500\n   }\n}",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/logout"      }    ]  },  {    "type": "post",    "url": "/auth/register",    "title": "用户注册",    "version": "1.0.0",    "name": "user_register",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "account",            "description": "<p>用户账号(手机号)</p>"          },          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "password",            "description": "<p>设置密码</p>"          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n   \"meta\": {\n     \"message\": \"Success\",\n     \"status_code\": 200\n   }\n }",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/register"      }    ]  },  {    "type": "post",    "url": "/auth/upToken",    "title": "更新或换取新Token",    "version": "1.0.0",    "name": "user_token",    "group": "User",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "string",            "optional": false,            "field": "token",            "description": ""          }        ]      }    },    "success": {      "examples": [        {          "title": "成功响应:",          "content": "{\n    \"meta\": {\n      \"message\": \"更新Token成功！\",\n      \"status_code\": 200\n    },\n    \"data\": {\n      \"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9\"\n   }\n  }",          "type": "json"        }      ]    },    "filename": "app/Http/Controllers/Api/V1/AuthenticateController.php",    "groupTitle": "User",    "sampleRequest": [      {        "url": "http://saas.me/auth/upToken"      }    ]  }] });
