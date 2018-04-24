/**
 * Created by buyingfei on 2018/4/17.
 */

import request from '@/utils/request'

export function getDetail(data) {
  return request({
    url: 'news/detail',
    method: 'post',
    data: data
  })
}

export function getList(data) {
  return request({
    url: 'news/list',
    method: 'post',
    data: data
  })
}

export function modifyNews(data) {
  return request({
    url: 'news/modifyNews',
    method: 'post',
    data: data
  })
}

