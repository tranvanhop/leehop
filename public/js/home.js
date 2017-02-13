function getMyFriend()
{
    var url = url_base + '/home/getMyFriend';

    $.get(url, {}, function(data)
    {
        $('#my-friend').html(data);
    });
}

function getPostsLatest()
{
    var url = url_base + '/home/getPostsLatest';

    $.get(url, {}, function(data)
    {
        $('#posts-latest').html(data);
    });
}

function getSkills()
{
    var url = url_base + '/home/getSkills';

    $.get(url, {}, function(data)
    {
        $('#skills').html(data);
    });
}

function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#preview').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#image").change(function () {
    readURL(this);
});