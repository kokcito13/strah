var Tasks = Backbone.Collection.extend({
    url: __urlGetPosts
});

var PostListView = Backbone.View.extend({
    el: '#masonry_wrap',
    nextItems: [],
    page: 1,
    render: function () {
        var that = this;
        if (that.page > 1 && that.nextItems.length > 0){
            var template = _.template($('#post-template').html(), {posts: that.nextItems});
            var items = $(template);
            $container.prepend( items );
            $container.masonry('appended', items, 'reloadItems');
        }
        var tasks = new Tasks();
        if (that.page == 1) {
            tasks.fetch({
                data: {'page':1},
                success: function (posts) {
                    that.nextItems = posts.models;
                    var template = _.template($('#post-template').html(), {posts: that.nextItems});
                    var items = $(template);
                    $container.prepend( items );
                    $container.masonry('appended', items, 'reloadItems');
                }
            })
            that.page++;
        }

        setTimeout(function(){
            tasks.fetch({
                data: {'page':that.page},
                success: function (posts) {
                    that.nextItems = posts.models;
                    if (that.nextItems.length > 0){
                        $('.load-more').show();
                    } else {
                        $('.load-more').hide();
                    }
                    that.page++;
                }
            })
        }, 200);

    }
});

var postListView = new PostListView();
$(function(){
    postListView.render();
    $('.load-more').click(function(e){
        e.preventDefault();
        postListView.render();
    });
});