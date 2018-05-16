function getXmlHttp() {
    var xmlhttp;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            xmlhttp = false;
        }
    }
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
};


function flogin_signin(email, passwd) {
    var xmlhttp = getXmlHttp();
    var result = '';
    xmlhttp.open('POST', 'signin.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = 'email=' + email + '&passwd=' + passwd;
    console.log(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                result = xmlhttp.responseText;
                console.log(result);
                if (result == 'NO_USER_OR_PASS') {
                    alert('Enter correct email or password');
                }
                else {
                    //window.location.href = "http://localhost/makefake.org/index.php";
                    console.log('��ࠡ��뢠��');
                }
            }
        }
    }
};

function flogin_signup(login, passwd) {
    var zapros = 'login=' + login + '&passwd=' + passwd;
    console.log(zapros);
    var xmlhttp = getXmlHttp();
    var signup_error = '<div class="alert alert-error fade in"><button class="close" type="button" data-dismiss="alert">x</button><br>User already exists</div>';
    xmlhttp.open('POST', 'signup.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                result = xmlhttp.responseText;
                console.log(result);
                if (result == 'USER_EXISTS') document.getElementById('error_signup').innerHTML = signup_error;
                else {
                    document.getElementById('MyInnFriends').innerHTML = result;
                    $('#SignUp').modal('hide');
                }
            }
        }
    }
};


function SaveAccountInformation(id, firstname, secondname, country, lang, birthday) {
    var xmlhttp = getXmlHttp();
    var result = '';
    xmlhttp.open('POST', 'saveaccountinformation.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = 'firstname=' + firstname + '&secondname=' + secondname + '&country=' + country + '&lang=' + lang + '&birthday=' + birthday;
    console.log(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                result = xmlhttp.responseText;
                console.log(result);
                if (result == id) $('#AccountSettings').modal('hide');
            }
        }
    }
};


var topics_full = new Array(); // 0 - 3 ���������, 1 - ��
var read_comments = new Array(); // ������⢮ �������ਥ� � ⮯���
var read_comments_full = new Array();
var show_comments = new Array();

// �⠥� �� ���������, �᫨ �� ����� 3�, � �� �뤠����
function ReadComments(topic_id) {
    post = document.getElementById('public_post' + topic_id);
    xy = post.getBoundingClientRect();
    if (xy.top > 800 || xy.bottom < 0) {
        console.log('**************** PROPUSK *********************************');
        return;
    }

    show_comments[topic_id] = 1;

    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'getcomments.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
//	alert(topic_id);
    var zapros = 'topic_id=' + topic_id;
    var key;
    var toUserName = '';
//	alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                result = xmlhttp.responseText;
                //document.getElementById(topic_id).innerHTML = xmlhttp.responseText;

                //console.log('JSON-result for post '+topic_id+' ='+result);
                if (!result == 0) {
                    var commsg = JSON.parse(result);
                    console.log(result);

                    if ((commsg.length <= 3) && (commsg.length > read_comments[topic_id])) {
                        var message = '';
                        for (key = 0; key < commsg.length; key++) {
                            var block = '';
                            if (commsg[key].msg_type == 1) {
                                block = '<br><img src="uploads/' + commsg[key].msg_foto + '.jpg">';
                            }
                            var commLikes = '';
                            if (commsg[key].msg_likes > 0) commLikes = commsg[key].msg_likes;
                            if (commsg[key].touser_name != '0') toUserName = '<small> replied ' + commsg[key].touser_name + '</small>';
                            message += '<div class="comment"><div id="image"><img src="users-photoes/' + commsg[key].id + '.jpg" width="50" class="img-circle"></div><div id="post"><strong>' + commsg[key].name + '</strong>' + toUserName + '<br><pre>' + commsg[key].msgtext + block + '</pre></div><div id="time' + commsg[key].msg_foto + '" class="time"></div><a href="" class="reply" onclick="reply(' + topic_id + ',\'' + commsg[key].name + '\',' + commsg[key].id + '); return false;">reply</a><div class="like"><span id="like' + commsg[key].msg_foto + '">' + commLikes + '</span><i class="icon-heart" onclick="like(' + commsg[key].msg_foto + ')"></i></div></div><hr>';
                        }
                        document.getElementById(topic_id).innerHTML = message;
                        for (var key = 0; key < commsg.length; key++) {
                            parseTime(commsg[key].msg_date, "time" + commsg[key].msg_foto);
                            var span = document.getElementById('time' + commsg[key].msg_foto);
                            var timescript = document.createElement('script');
                            timescript.innerHTML = 'setInterval(\'parseTime("' + commsg[key].msg_date + '","time' + commsg[key].msg_foto + '")\', 10000)';
                            span.appendChild(timescript);
                        }
                        read_comments[topic_id] = commsg.length;
                    }


                    if (commsg.length > read_comments[topic_id]) {
                        if (commsg.length > 3) document.getElementById(topic_id).innerHTML = "<a onclick=\"ReadCommentsFull(" + topic_id + ");\">Show the previous " + (commsg.length - 3) + "</a>";

                        for (key = commsg.length - 3; key < commsg.length; key++) {
                            var block = '';
                            if (commsg[key].msg_type == 1) {
                                block = '<br><img src="uploads/' + commsg[key].msg_foto + '.jpg">';
                            }
                            //console.log("Kommentariy viveden");
                            var commLikes = '';
                            if (commsg[key].msg_likes > 0) commLikes = commsg[key].msg_likes;
                            if (commsg[key].touser_name != '0') toUserName = '<small> replied ' + commsg[key].touser_name + '</small>';
                            document.getElementById(topic_id).innerHTML += '<div class="comment"><div id="image"><img src="users-photoes/' + commsg[key].id + '.jpg" width="50" class="img-circle"></div><div id="post"><strong>' + commsg[key].name + '</strong>' + toUserName + '<br><pre>' + commsg[key].msgtext + block + '</pre></div><div id="time' + commsg[key].msg_foto + '" class="time"></div><a href="" class="reply" onclick="reply(' + topic_id + ',\'' + commsg[key].name + '\',' + commsg[key].id + '); return false;">reply</a><div class="like"><span id="like' + commsg[key].msg_foto + '">' + commLikes + '</span><i class="icon-heart" onclick="like(' + commsg[key].msg_foto + ')"></i></div></div><hr>';
                            parseTime(commsg[key].msg_date, "time" + commsg[key].msg_foto);
                            var span = document.getElementById('time' + commsg[key].msg_foto);
                            var timescript = document.createElement('script');
                            timescript.innerHTML = 'setInterval(\'parseTime("' + commsg[key].msg_date + '","time' + commsg[key].msg_foto + '")\', 10000)';
                            span.appendChild(timescript);

                        }
                        read_comments[topic_id] = commsg.length;
                    }
                }
            }
        }
    }
}

function reply(topic, userName, userId) {
    $('#Reply' + topic).val(userId);
    document.getElementById('ReplyDiv' + topic).innerHTML = '<span class="label label-info">Reply to ' + userName + '<span class="pointer" title="Do not send" onclick="DeleteReplyFromMessage(' + topic + ');"> x </span></span>';
    $('#formSendMessage' + topic + ' textarea').val(userName + ', ');
    $('#formSendMessage' + topic + ' textarea').focus();
}

function DeleteReplyFromMessage(topic) {
    document.getElementById('ReplyDiv' + topic).innerHTML = '';
    $('#Reply' + topic).val(0);
    $('#formSendMessage' + topic + ' textarea').val('');
}

// �⠥� �� �������ਨ
function ReadCommentsFull(topic_id) {
    topics_full[topic_id] = 1;
    var toUserName = '';
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'getcomments.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = 'topic_id=' + topic_id;
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                result = xmlhttp.responseText;
                var commsg = JSON.parse(result);
                var msg = '';
                if (commsg.length > read_comments_full[topic_id]) {
                    for (var key = 0; key < commsg.length; key++) {
                        //console.log('id='+commsg[key].id+' name='+commsg[key].name+' msgtext='+commsg[key].msgtext+' msg_type='+commsg[key].msg_type);
                        var block = '';
                        var commLikes = '';
                        if (commsg[key].msg_likes > 0) commLikes = commsg[key].msg_likes;
                        if (commsg[key].touser_name != '0') toUserName = '<small> replied ' + commsg[key].touser_name + '</small>';
                        if (commsg[key].msg_type == 1) {
                            block = '<br><img src="uploads/' + commsg[key].msg_foto + '.jpg">';
                        }

                        msg += '<div class="comment"><div id="image"><img src="users-photoes/' + commsg[key].id + '.jpg" width="50" class="img-circle"></div><div id="post"><strong id="name' + commsg[key].id + '">' + commsg[key].name + '</strong>' + toUserName + '<br><pre>' + commsg[key].msgtext + block + '</pre></div><div id="time' + commsg[key].msg_foto + '" class="time"></div><a href="" class="reply" onclick="reply(' + topic_id + ',\'' + commsg[key].name + '\'); return false;">reply</a><div class="like"><span id="like' + commsg[key].msg_foto + '">' + commLikes + '</span><i class="icon-heart" onclick="like(' + commsg[key].msg_foto + ')"></i></div></div><hr>';

                    }
                    document.getElementById(topic_id).innerHTML = msg;

                    for (var key = 0; key < commsg.length; key++) {
                        parseTime(commsg[key].msg_date, "time" + commsg[key].msg_foto);
                        var span = document.getElementById('time' + commsg[key].msg_foto);
                        var timescript = document.createElement('script');
                        timescript.innerHTML = 'setInterval(\'parseTime("' + commsg[key].msg_date + '","time' + commsg[key].msg_foto + '")\', 10000)';
                        span.appendChild(timescript);
                    }
                    read_comments_full[topic_id] = commsg.length;

                }
            }
        }
    }

}


function parseTime(message_date, id) {
    if (!document.getElementById(id)) return;
    var curDate = moment();
    var msgDate = moment(message_date);
    var raznitca = curDate - msgDate;
    var dayStart = curDate
    if (raznitca < 59999) document.getElementById(id).innerHTML = Math.round((raznitca) / 1000) + ' seconds ago';
    if (raznitca > 60000 && raznitca < 3600000) document.getElementById(id).innerHTML = Math.round((raznitca) / 60000) + ' minutes ago';
    if (raznitca > 3600000 && raznitca < 3600000 * 4) document.getElementById(id).innerHTML = Math.round((raznitca) / 3600000) + ' hours ago';
    if (raznitca > 3600000 * 4) document.getElementById(id).innerHTML = msgDate.format('MMMM Do YYYY, h:mm a');
};

function parseTopicTime(message_date) {
    var curDate = moment();
    var msgDate = moment(message_date);
    raznitca = curDate - msgDate;
    dayStart = curDate
    if (raznitca < 59999) return Math.round((raznitca) / 1000) + ' seconds ago';
    if (raznitca > 60000 && raznitca < 3600000) return Math.round((raznitca) / 60000) + ' minutes ago';
    if (raznitca > 3600000 && raznitca < 3600000 * 4) return Math.round((raznitca) / 3600000) + ' hours ago';
    if (raznitca > 3600000 * 4) return msgDate.format('MMMM Do YYYY, h:mm a');
};

function like(comment_id) {
    //alert(avatars_id[current_avatar]);
    var resp = '';
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'likes.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "comment_id=" + encodeURIComponent(comment_id) + "&user_id=" + encodeURIComponent(avatars_id[current_avatar]);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                count = parseInt(xmlhttp.responseText);
                currentCount = parseInt(document.getElementById('like' + comment_id).innerText);
                if (isNaN(currentCount)) currentCount = 0;
                document.getElementById('like' + comment_id).innerText = currentCount + count;
            }
        }
    }
};

function likeTopic(topic_id) {
    //alert(avatars_id[current_avatar]);
    var resp = '';
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'topiclikes.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "topic_id=" + encodeURIComponent(topic_id) + "&user_id=" + encodeURIComponent(avatars_id[current_avatar]);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                count = parseInt(xmlhttp.responseText);
                currentCount = parseInt(document.getElementById('topiclike' + topic_id).innerText);
                if (isNaN(currentCount)) currentCount = 0;
                document.getElementById('topiclike' + topic_id).innerText = currentCount + count;
            }
        }
    }
};

function UpdateComments(topic_id) {
    if (topics_full[topic_id] == 1) ReadCommentsFull(topic_id)
    else ReadComments(topic_id);
};

// �뢮��� �������ਨ ��室� ���� ᯨ᮪ ⮯���� - �� �ᯮ������
function ReadAllComments() {
    var elems = document.getElementsByClassName('topic');
    for (var i = 0; i < elems.length; i++) {
        read_comments[elems[i].id] = 0;
        read_comments_full[elems[i].id] = 0;
        ReadComments(elems[i].id);
    }
    ;
};


// �� �ᯮ������
function Start() {
    //GetOnlineVirtualUser();
    //setInterval(ReadTopics(public_id),300000);
    ReadAllComments();
    //setInterval(ReadAllComments, 10000);
};

function StartAllPages() {
    GetOnlineVirtualUser();
};


var current_avatar = 0;


function SendMessageToBd(forma) {
    var formData = new FormData(forma);
    formData.append('avid', avatars_id[current_avatar]);
    console.log(formData);
    $.ajax({
        url: 'addcomment.php',
        type: "POST",
        data: formData,
        async: false,
        success: function (msg) {
            //alert(msg);
        },
        error: function (msg) {
            alert('�訡��!');
        },
        cache: false,
        contentType: false,
        processData: false
    });
    forma.reset();
    DeleteFotoFromMessage(forma.TOP_id.value);
    DeleteReplyFromMessage(forma.TOP_id.value);
    UpdateComments(forma.TOP_id.value);
}

function HidePublic(public_id, hide_status) {
    var resp = '';
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'hidepublic.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "public_id=" + encodeURIComponent(public_id) + "&hide_status=" + encodeURIComponent(hide_status);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                console.log('Otvet=' + xmlhttp.responseText);
                if (xmlhttp.responseText == 0) {
                    console.log('text=0');
                    document.getElementById('hide_public').innerHTML = '<a href="#" onclick="HidePublic(public_id, 1);">Show public</a>';
                }
                if (xmlhttp.responseText == 1) {
                    console.log('text=1');
                    document.getElementById('hide_public').innerHTML = '<a href="#" onclick="HidePublic(public_id, 0);">Hide public</a>';
                }
            }
        }
    }

}

function followPublic(public_id, follow_status) {
    var resp = '';
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'followpublic.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "public_id=" + encodeURIComponent(public_id) + "&follow_status=" + encodeURIComponent(follow_status);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                console.log('Otvet=' + xmlhttp.responseText);
                if (xmlhttp.responseText == 0) {
                    console.log('text=0');
                    document.getElementById('follow_public').innerHTML = '<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo" onclick="followPublic(public_id, 1);">Follow this public</a>';
                }
                if (xmlhttp.responseText == 1) {
                    console.log('text=1');
                    document.getElementById('follow_public').innerHTML = '<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo" onclick="followPublic(public_id, 0);">Exit from followers</a>';
                }
            }
        }
    }
};

var FotoInMessage = 0;
var FileName = "";

function loadfile(name, id) {
    alert(name);
    element_id = id;
    var FileNameCount = '';
    FileName = name.split('\\').pop();
    console.log(FileName);
    if (FileName.length > 20) {
        FileNameCount = FileName.slice(0, 10) + '...' + FileName.slice(FileName.length - 10, FileName.length);
    } else FileNameCount = FileName;
    msgs_foto = "#MSGfoto" + element_id.replace(/\D/g, '');
    ;
    console.log(msgs_foto);
    document.getElementById('MSGfoto' + element_id.replace(/\D/g, '')).innerHTML = '<span class="label label-info">' + FileNameCount + ' will add to message <span class="pointer" title="Delete photo" onclick="DeleteFotoFromMessage(' + element_id.replace(/\D/g, '') + ');"> x </span></span>';
    //$(this).next('label').html('<i class="fa fa-file"></i> ' + FileName);

    $('#MSGfoto' + element_id.replace(/\D/g, '')).css("display", "block");
    FotoInMessage = 1;
    document.getElementById('MSG_type' + element_id.replace(/\D/g, '')).value = 1;
    console.log(FotoInMessage);
};


var FotoInTopic = 0;
var FileNameTopic = "";

function loadfile_topic(name) {
    alert('from topic' + name);
    var FileNameCount = '';
    FileName = name.split('\\').pop();
    console.log('from topic' + FileName);
    if (FileName.length > 20) {
        FileNameCount = FileName.slice(0, 10) + '...' + FileName.slice(FileName.length - 10, FileName.length);
    } else FileNameCount = FileName;
    document.getElementById('TOPICfoto').innerHTML = '<span class="label label-info">' + FileNameCount + ' will add to message <span class="pointer" title="Delete photo" onclick="DeleteFotoFromTopic();"> x </span></span>';

    $('#TOPICfoto').css("display", "block");
    FotoInMessage = 1;
    document.getElementById('TOPIC_type').value = 1;
    console.log(FotoInMessage);
}

function DeleteFotoFromTopic() {
    $('#TOPICfoto').css("display", "none");
    FotoInTopic = 0;
    FileNameTopic = "";
    document.getElementById('TOPIC_type').value = 0;
}

function DeleteFotoFromMessage(topic_id) {
    $('#MSGfoto' + topic_id).css("display", "none");
    FotoInMessage = 0;
    FileName = "";
    document.getElementById('MSG_type' + topic_id).value = 0;
}


function loadfile_offer(name) {
    alert('from offer' + name);
    var FileNameCount = '';
    FileName = name.split('\\').pop();
    console.log('from offer' + FileName);
    if (FileName.length > 20) {
        FileNameCount = FileName.slice(0, 10) + '...' + FileName.slice(FileName.length - 10, FileName.length);
    } else FileNameCount = FileName;
    document.getElementById('OFFERfoto').innerHTML = '<span class="label label-info">' + FileNameCount + ' will add to message <span class="pointer" title="Delete photo" onclick="DeleteFotoFromOffer();"> x </span></span>';
    FotoInMessage = 1;
    document.getElementById('OFFER_type').value = 1;
    $('#OFFERfoto').css("display", "block");
    console.log(FotoInMessage);
}

function DeleteFotoFromOffer() {
    $('#OFFERfoto').css("display", "none");
    FotoInTopic = 0;
    FileNameTopic = "";
    document.getElementById('OFFER_type').value = 0;
}


var topics_count = 0;
var height = $(window).height();

function TopicScroll() {
    var scroll_height = $(window).scrollTop();
    console.log('height =' + height, 'scroll_height=' + scroll_height);
    if (scroll_height >= (height - 300)) {
        topics_count += 3;
        height += 450;
        ReadTopics(public_id);
    }
};

var wallTopicsCount = 0;
var wallHeight = 400;

function WallScroll(user_id) {
    var scroll_height = $(window).scrollTop();
    console.log('height =' + wallHeight, 'scroll_height=' + scroll_height, 'wallTopicsCount=' + wallTopicsCount);
    if (scroll_height >= wallHeight) {
        wallTopicsCount += 3;
        wallHeight += 400;
        ReadNewsWall(user_id);
    }
};


function OfferTopicScroll() {
    var scroll_height = $(window).scrollTop();
    console.log('height =' + height, 'scroll_height=' + scroll_height);
    if (scroll_height >= height) {
        topics_count += 10;
        height += 450;
        ReadOfferedTopics(public_id);
    }
};

function deleteTopic(topic_id) {
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'deletetopic.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "topic_id=" + encodeURIComponent(topic_id);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                console.log('topic ' + topic_id + ' deleted');
                if (xmlhttp.responseText == 1) document.getElementById('public_post' + topic_id).style.display = 'none';
            }
        }
    }

}

function deleteOfferedTopic(topic_id) {
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'deleteofferedtopic.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "topic_id=" + encodeURIComponent(topic_id);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                console.log('topic ' + topic_id + ' deleted');
                if (xmlhttp.responseText == 1) document.getElementById('public_post' + topic_id).style.display = 'none';
            }
        }
    }

}

function ReadTopics(public_id) {
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'gettopics.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "public_id=" + encodeURIComponent(public_id) + "&topics_count=" + encodeURIComponent(topics_count);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                if (!xmlhttp.responseText == 0) {

                    var topics = JSON.parse(xmlhttp.responseText);
                    console.log(xmlhttp.responseText);

                    for (key = topics_count; key < topics.length; key++) {
                        var echo = '';
                        var topic_img = '';
                        console.log('topic=' + topics[key].topic_id + ' topics_start=' + topics_count + ' key=' + key);
                        var topicLikes = '';
                        if (topics[key].topic_likes > 0) topicLikes = topics[key].topic_likes;

                        var photo = topics[key].topic_id;
                        if (topics[key].topic_repost > 0) photo = topics[key].topic_repost;


                        if (topics[key].topic_type == 1) topic_img = '<img src="topic_uploads/' + photo + '.jpg" class="topicimage">';
                        var deletePost = '';
                        if (publicAdmin == 1) deletePost = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="deleteTopic(' + topics[key].topic_id + ');"><i class="icon-remove"></i></button>';
                        echo += '<div class="down-line left10px public_time">' + deletePost + '<p>' + topics[key].topic_text + '</p>\
					' + topic_img + '\
					<div id="topic_time' + topics[key].topic_id + '" class="topic_time"><span>' + parseTopicTime(topics[key].topic_time) + '</span><span id="repost"></span><i class="icon-retweet" onclick="repost(' + topics[key].topic_id + ')"></i><span id="topiclike' + topics[key].topic_id + '" class="tl_font">' + topicLikes + '</span><i class="icon-heart" onclick="likeTopic(' + topics[key].topic_id + ')"></i></div>\
					</div>\
					<div id="' + topics[key].topic_id + '" class=\"topic\"></div>\
					<div id="public_post_textarea">\
						<form id="formSendMessage' + topics[key].topic_id + '" name="formSendMessage' + topics[key].topic_id + '" method="post" enctype="multipart/form-data" onsubmit="return false;">\
							<input id="Reply' + topics[key].topic_id + '" name="reply" type="hidden" value="0">\
							<div id="ReplyDiv' + topics[key].topic_id + '"></div>\
							<textarea type="text" name="comment" id="comment" placeholder="What do you think of it??" rows="2" onfocus="this.rows=4" onblur="textarea_onblur(' + topics[key].topic_id + ')" class="span12"></textarea>\
							<span class="edit center" id="MSGfoto' + topics[key].topic_id + '" style="display: none;"></span>\
							<input id="MSG_type' + topics[key].topic_id + '" name="MSG_type" type="hidden" value="0">\
							<input id="TOP_id' + topics[key].topic_id + '" name="TOP_id" type="hidden" value="' + topics[key].topic_id + '">\
							<div class="send_form">\
								<div id="send_comment" name="send_comment" class="polovina" onclick="SendMessageToBd(document.getElementById(\'formSendMessage' + topics[key].topic_id + '\'));document.formSendMessage' + topics[key].topic_id + '.reset();"><span>Send</span></div>\
								<div class="polovina40">\
									<input type="file" name="userfile" id="userfile' + topics[key].topic_id + '" class="file-select" onchange="loadfile(this.value, this.id);"><label for="userfile' + topics[key].topic_id + '"></label>\
								</div>\
							</div>\
						</form>\
					</div>\
					</div>';

                        //document.getElementById('public').innerHTML += echo;
                        var divPublic = document.getElementById("public");
                        newdiv = document.createElement('div');
                        newdiv.setAttribute("id", "public_post" + topics[key].topic_id);
                        newdiv.setAttribute("class", "public_post");
                        newdiv.innerHTML += echo;
                        divPublic.appendChild(newdiv);

                        read_comments[topics[key].topic_id] = 0;
                        read_comments_full[topics[key].topic_id] = 0;
                        UpdateComments(topics[key].topic_id);
                        // ��⠢�塞 � DOM �ਯ� ����㧪� �������ਥ�
                        var div = document.getElementById(topics[key].topic_id);
                        //console.log(div);
                        var newscript = document.createElement('script');
                        newscript.innerHTML = 'setInterval(\'UpdateComments(' + topics[key].topic_id + ')\',15000);';
                        //newscript.innerHTML = 'setInterval(\'console.log("SCRIPT RABOTAET")\', 5000);';
                        div.appendChild(newscript);
                    }
                }

            }
        }
    }

};


function ReadOfferedTopics(public_id) {
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'getofferedtopics.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "public_id=" + encodeURIComponent(public_id) + "&topics_count=" + encodeURIComponent(topics_count);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                if (!xmlhttp.responseText == 0) {

                    var topics = JSON.parse(xmlhttp.responseText);
                    console.log(xmlhttp.responseText);

                    for (key = topics_count; key < topics.length; key++) {
                        var echo = '';
                        var topic_img = '';
                        console.log('topic=' + topics[key].topic_id + ' topics_start=' + topics_count + ' key=' + key);
                        var topicLikes = '';
                        if (topics[key].topic_likes > 0) topicLikes = topics[key].topic_likes;

                        var photo = topics[key].topic_id;
                        if (topics[key].topic_repost > 0) photo = topics[key].topic_repost;


                        if (topics[key].topic_type == 1) topic_img = '<img src="topic_offers/' + photo + '.jpg" class="topicimage">';
                        var deletePost = '';
                        if (publicAdmin == 1) deletePost = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="deleteOfferedTopic(' + topics[key].topic_id + ');"><i class="icon-remove"></i></button>';
                        echo += '<div class="left10px public_time">' + deletePost + '<p>' + topics[key].topic_text + '</p>\
					' + topic_img + '\
					</div>\
						<div class="send_form">\
						<div id="send_comment" name="send_comment" onclick="SendMessageToBd(document.getElementById(\'formSendMessage' + topics[key].topic_id + '\'));document.formSendMessage' + topics[key].topic_id + '.reset();"><span>Publish</span></div>\
						<div id="del_topic" name="del_topic" onclick="deleteOfferedTopic(' + topics[key].topic_id + ');"><span>Fail</span></div>\
						</div>\
					</div>';

                        //document.getElementById('public').innerHTML += echo;
                        var divPublic = document.getElementById("public");
                        newdiv = document.createElement('div');
                        newdiv.setAttribute("id", "public_post" + topics[key].topic_id);
                        newdiv.setAttribute("class", "public_post");
                        newdiv.innerHTML += echo;
                        divPublic.appendChild(newdiv);

                        //read_comments[topics[key].topic_id]=0;
                        //read_comments_full[topics[key].topic_id]=0;
                        //UpdateComments(topics[key].topic_id);
                        // ��⠢�塞 � DOM �ਯ� ����㧪� �������ਥ�
                        //var div = document.getElementById(topics[key].topic_id);
                        //console.log(div);
                        //var newscript = document.createElement('script');
                        //newscript.innerHTML = 'setInterval(\'UpdateComments('+topics[key].topic_id+')\',15000);';
                        //newscript.innerHTML = 'setInterval(\'console.log("SCRIPT RABOTAET")\', 5000);';
                        //div.appendChild(newscript);
                    }
                }

            }
        }
    }

};


function saveRepost(public_id) {
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'repost.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "public_id=" + encodeURIComponent(public_id) + "&repost_id=" + encodeURIComponent(RepostId);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                console.log('Otvet=' + xmlhttp.responseText);
                alert("Repost complete");

            }
        }
    }

}


function ReadNewsWall(user_id) {
    var xmlhttp = getXmlHttp();
    xmlhttp.open('POST', 'getnewswall.php', true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var zapros = "user_id=" + encodeURIComponent(user_id) + "&topics_count=" + encodeURIComponent(wallTopicsCount);
    //alert(zapros);
    xmlhttp.send(zapros);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {

                if (!xmlhttp.responseText == 0) {

                    var topics = JSON.parse(xmlhttp.responseText);
                    console.log(xmlhttp.responseText);

                    for (key = wallTopicsCount; key < topics.length; key++) {
                        var echo = '';
                        var topic_img = '';
                        console.log('topic=' + topics[key].topic_id + ' topics_start=' + wallTopicsCount + ' key=' + key);
                        var topicLikes = '';
                        if (topics[key].topic_likes > 0) topicLikes = topics[key].topic_likes;

                        var photo = topics[key].topic_id;
                        if (topics[key].topic_repost > 0) photo = topics[key].topic_repost;


                        if (topics[key].topic_type == 1) topic_img = '<img src="topic_uploads/' + photo + '.jpg" class="topicimage">';
                        echo += '<a href="/' + topics[key].topic_community + '"><div class="left10px public_time">\
					<div class="top_header"><img src="publics-logo/' + topics[key].topic_community + '.jpg" class="img-circle" width=50px"><span>' + topics[key].topic_comm_name + '</span></div><p class="black">' + topics[key].topic_text + '</p>\
					' + topic_img + '\
					<div id="topic_time' + topics[key].topic_id + '" class="topic_time"><span>' + parseTopicTime(topics[key].topic_time) + '</span><span id="topiclike' + topics[key].topic_id + '" class="tl_font">' + topicLikes + '</span><i class="icon-heart"></i></div>\
					</div>\
					<div id="' + topics[key].topic_id + '" class=\"topic\"></div>\
					</div>\
					</div></a>';

                        //document.getElementById('public').innerHTML += echo;
                        var divPublic = document.getElementById("public");
                        newdiv = document.createElement('div');
                        newdiv.setAttribute("id", "public_post" + topics[key].topic_id);
                        newdiv.setAttribute("class", "public_post");
                        newdiv.innerHTML += echo;
                        divPublic.appendChild(newdiv);


                    }
                }

            }
        }
    }

};