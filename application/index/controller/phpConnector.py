# -- coding: utf-8 --
_jsonp_begin = r'jsonp177('
_jsonp_end = r');'
import requests, json, sys

def searchWithoutParam(dep, arr, date):

    url = "https://sjipiao.fliggy.com/searchow/search.htm?"

    headers = {
        'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',
        'referer': 'https://sjipiao.fliggy.com/flight_search_result.htm?searchBy=1280&ttid=sem.000000736&tripType=0&depCityName=&depCity=&arrCityName=&arrCity=&depDate=2020-02-19&arrDate=',
        'cookie': 'cna=pqriFC1D1QgCAXEQLR1Fs9Gx; hng=CN%7Czh-CN%7CCNY%7C156; t=80296ff8eedaf9e3fda960db2f883bd3; tracknick=%5Cu9E87%5Cu6DE4%5Cu80BC; lid=%E9%BA%87%E6%B7%A4%E8%82%BC; enc=GoWj2HOMfY5Fh80P4S1og1vfYnLsqyCuk4MwhqlwwqUTc7DOYJFfWCPsmdQQGTU7VSsEufORwslwCLqIu1RUbA%3D%3D; _tb_token_=e393636de3317; cookie2=36186b3c4efc57b659e38a026dbd1632; UM_distinctid=16ecc4a1e867a8-0639f050002852-2393f61-1fa400-16ecc4a1e87ca2; CNZZDATA30066717=cnzz_eid%3D818807737-1575383487-https%253A%252F%252Fwww.fliggy.com%252F%26ntime%3D1575383487; l=dBMrssKVq8lIfNHDBOfZlurza77tfIRb8AVzaNbMiICPOxCp52sRWZKz4pY9CnhV3st6R3Jt3efYBlL3XPa9hBNSyUBfnwqZ1dTeR; isg=BKqqA7bvg4PQXQ_vHYNl6xN--xBMGy51hVV-djRjuP2IZ0shHKpph_6V9tNepqYN'
    }

    param = "_ksTS=1575387218097_176&callback=jsonp177&tripType=0&depCity=&depCityName="+dep+"&arrCity=&arrCityName="+arr+"&depDate="+date+"&searchSource=99&searchBy=1280&sKey=&qid=&needMemberPrice=true&_input_charset=utf-8&ua=090%23qCQX1TX2X2OXPXi0XXXXXQkOOzpMk9ZnfQVZ3%2BmuAGBpfHLosxZGGlCvIH9EkU9s3vQXi3e7PUa%2FXvXuZW4R4tG0wvQXQZ%2FPKHWDb%2Frw%2FvlcDGmGgWDHOvnNXvXnh9TXXP73OzueGrXVHYVm5ehnLXj3HoDIh9k4aP73IfgeG2XPHYVyqxViXXjPjXf12z0bomQiXXKCVf0IqjLtXvXQ0ZsNLv%3D%3D&openCb=true"

    url_main = url + param
    res = requests.get(url=url_main, headers=headers)
    data = res.text
    return data

def from_jsonp(jsonp_str):
    jsonp_str = jsonp_str.strip()
    if not jsonp_str.startswith(_jsonp_begin) or \
            not jsonp_str.endswith(_jsonp_end):
        raise ValueError('Invalid JSONP')
    return json.loads(jsonp_str[len(_jsonp_begin):-len(_jsonp_end)])

dep = sys.argv[1]
arr = sys.argv[2]
date = sys.argv[3]
# dep = '南宁'
# arr = '北京'
# date = '2020-02-20'
data_res = searchWithoutParam(dep=dep, arr=arr, date=date)
jsonp_parse_data = from_jsonp(data_res)
json_form_data = json.dumps(jsonp_parse_data)
print(json_form_data)

