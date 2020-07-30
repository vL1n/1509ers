/*
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This is a demo file used only for the main dashboard (login.html)
 **/

$(function () {

  'use strict'

  // Make the dashboard widgets sortable Using jquery UI
  $('.connectedSortable').sortable({
    placeholder: 'sort-highlight',
    connectWith: '.connectedSortable',
    handle: '.card-header, .nav-tabs',
    forcePlaceholderSize: true,
    zIndex: 999999
  })
  $('.connectedSortable .card-header, .connectedSortable .nav-tabs-custom').css('cursor', 'move')

})

$(document).ready(function () {
  var path = window.location.pathname;
  var path2 = path.split('/');
  var tree = path2[2];
  var obj = document.getElementById(tree)
  var title = document.getElementById('html_title')
  title.innerHTML = path2[2]+'-1509ers';
  if(obj){
    obj.setAttribute("class","nav-link active")
  }
});
