var Ealt = new Eject()
var phoneChecked = true
var pwdChanged = true

// 验证码自动跳转
$("input[name^='code']").each(function(){
    $(this).keyup(function(e){
        if($(this).val().length < 1){
            $(this).prev().focus();
        }else{
            if($(this).val().length >= 1){
                $(this).next().focus();
            }
        }
    });

});

// 点击登录按钮
$(function () {
    $("#loginBtn").on('click', function () {
        // 把消息栏清空
        $("#alertMsg").text('')
        $.ajax({
            type: 'POST',
            url: "{:url('/api/v1/Utils/userLogin')}",
            data: $("#loginForm").serialize(),
            dataType: "JSON",
            success: function (res) {
                patchSituations(JSON.parse(res))
            }
        })
    })
})

// 处理登录返回异常情况
function patchSituations(res) {
    switch (res.code) {
        // 手机号未验证或账号密码未修改
        case 1009:
            if(res.data.pwd_changed == 0){
                this.pwdChanged = false
                pwdChange(res.data.uid)
            }
            if(res.data.phone_checked == 0){
                this.phoneChecked = false
                phoneCheck(res.data.uid)
            }
            break;
        // 登陆成功
        case 0:
            window.location.href = '/test/index'
            break;
        default:
            $("#alert").show()
            $("#alertMsg").text(res.msg)
    }
}

// 手机号验证成功或者初始密码修改之后再次确认
function checkAgain(id) {
    if(!this.pwdChanged){
        pwdChange(id)
    }
    if(!this.phoneChecked){
        phoneCheck(id)
    }
    else{
        $("#loginRow").show()
        $("#phoneCheckRow").hide()
        $("#pwdChangeRow").hide()
        $("#loginBtn").click()
    }
}

// 渲染手机号验证模块
function phoneCheck(id) {
    $("#loginRow").hide()
    $("#phoneCheckRow").show()
    $("#pwdChangeRow").hide()
    $("#uid").val(id)
}

// 渲染初始密码更改模块
function pwdChange(id){
    $("#loginRow").hide()
    $("#phoneCheckRow").hide()
    $("#pwdChangeRow").show()
    $("#uid").val(id)
}

// 发送验证码按钮按下事件
$(function () {
    $("#button-addon2").on('click', function () {
        // 把消息栏清空
        $("#phoneCheckAlertMsg").text('')
        if ($("#phoneNumber").val() == ''){
            Ealt.Etoast('手机号不能为空！',2)
            return false
        }
        $("#button-addon2").attr({"disabled":"disabled"})
        $.ajax({
            type: 'POST',
            url: "{:url('/api/v1/Utils/sendSMS')}",
            data: {
                phone : $('#phoneNumber').val(),
                id : $('#uid').val()
            },
            dataType: "JSON",
            success: function (res) {
                switch (JSON.parse(res).code) {
                    // 验证码发送成功
                    case 0:
                        Ealt.Etoast('验证码发送成功！',2)
                        $("#button-addon2").text('已发送！')
                        break;
                    default:
                        $("#phoneCheckAlert").show()
                        $("#phoneCheckAlertMsg").text(JSON.parse(res).msg)
                }
            }
        })
    })
})

// 验证按钮按下事件
$(function () {
    $("#phoneCheckBtn").on('click', function () {
        // 把消息栏清空
        $("#phoneCheckAlertMsg").text('')
        var code = '';
        $("input[name^='code']").each(function () {
            code += $(this).val();
        });
        $.ajax({
            type: 'POST',
            url: "{:url('/api/v1/Utils/validateSMS')}",
            data: {
                phone: $('#phoneNumber').val(),
                code: code
            },
            dataType: "JSON",
            success: function (res) {
                switch (JSON.parse(res).code) {
                    case 0:
                        bindPhone($('#phoneNumber').val(), $('#uid').val())
                        break;
                    default:
                        $("#phoneCheckAlert").show()
                        $("#phoneCheckAlertMsg").text(JSON.parse(res).msg)
                }
            }
        })

    })
})

// 绑定手机事件
function bindPhone(phone, id) {
    // 把消息栏清空
    $("#phoneCheckAlertMsg").text('')
    $.ajax({
        type: 'POST',
        url: "{:url('/api/v1/Utils/bindPhoneToAccount')}",
        data: {
            phone: phone,
            id: id
        },
        dataType: "JSON",
        success: function (res) {
            switch (JSON.parse(res).code) {
                case 0:
                    this.phoneChecked = true
                    Ealt.Econfirm({
                        title:'Success',
                        message:'手机号绑定成功！',
                        define:function(){
                            phoneChecked = true
                            checkAgain($('#uid').val())
                        }
                    })
                    break;
                default:
                    $("#phoneCheckAlert").show()
                    $("#phoneCheckAlertMsg").text(JSON.parse(res).msg)
            }
        }
    })
}

$(function () {
    $("#pwdChangeBtn").on('click',function () {
        // 把消息栏清空
        $("#pwdChangeAlertMsg").text('')
        $.ajax({
            type: 'POST',
            url: "{:url('/api/v1/Utils/pwdChange')}",
            data: {
                new_password: $('#new_password').val(),
                new_password_confirm: $('#new_password_confirm').val(),
                id : $('#uid').val()
            },
            dataType: "JSON",
            success: function (res) {
                switch (JSON.parse(res).code) {
                    case 0:
                        this.pwdChanged =true
                        Ealt.Econfirm({
                            title:'Success',
                            message:'密码修改成功！',
                            define:function(){
                                pwdChanged = true
                                checkAgain($('#uid').val())
                            }
                        })
                        break;
                    default:
                        $("#pwdChangeAlert").show()
                        $("#pwdChangeAlertMsg").text(JSON.parse(res).msg)
                }
            }
        })
    })
})