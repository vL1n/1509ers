# -- coding:utf-8 --
import requests
import sys,json


def getMovies(id):
    url = "http://api.skyrj.com/api/movie?id="+id
    headers = {
        'User-Agen': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36'
    }
    res = requests.get(url=url, headers=headers)
    return res.text



id = sys.argv[1]
res_data = getMovies(id=id)
res_json_data =json.dumps(res_data)
print(res_json_data)