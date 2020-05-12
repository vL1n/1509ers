# -- coding:utf-8 --
import requests
import sys
import json


def getMovies(keyword):
    url = "https://app.51xuexiaoyi.com/api/v1/searchQuestion?keyword="+keyword
    headers = {
        'User-Agen': 'android',
        'device': 'AhXOAOAfuOUST1ELqjWjbYldmwx7u9iq9IimfjAHU8h5',
        'app-version': '1.0.6',
        'token': '0f2LA3REmnb3C3l6NMUfa3HPdb7397y3BbwsZgtJsxcE1eIpG8fwtqBsnjB0'
    }
    res = requests.get(url=url, headers=headers)
    return res.text



keyword = sys.argv[1]
res_data = getMovies(keyword=keyword)
res_json_data =json.dumps(res_data)
print(res_json_data)