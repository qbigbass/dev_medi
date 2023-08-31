$(window).on('load', function() {
	$('.banner-animated').addClass('banner-image-load');
});

$(function(){
	var openCommentForm = function(event){
		$(".form-with-comments").click();
	}
	$(document).on("click", ".open-form-with-comments", openCommentForm);
});

$(document).ready(function() {
	console.log('debug script.js')
	$('.like-brick').on('click', '.like', function () {
		console.log('debug click');
		let postId = $(this).attr('data-post-id');
		let doAction = '';

		if ($(this).hasClass('active')) {
			doAction = 'delete';
		} else {
			doAction = 'add';
		}

		console.log('debug postId = ', postId);
		console.log('debug doAction = ', doAction);
		addLike(postId, doAction);
	});

	function addLike(postId, action) {
		let param = 'postId='+postId+"&action="+action;

		console.log('debug param = ', param);

		$.ajax({
			url: '/ajax/likes/',
			type: 'GET',
			dataType: 'html',
			data: param,
			success: function (response) {
				let result = $.parseJSON(response);
				let likeCount;

				if (result == 1) {
					// Увеличение лайков
					$('.like[data-post-id="'+postId+'"]').addClass('active');
					let currentCount = parseInt($('.like .like-count').html());

					if (currentCount >= 0) {
						likeCount = currentCount + 1;
					}

					$('.like .like-count').html(likeCount);
				}

				if (result == 2) {
					// Уменьшение лайков
					$('.like[data-post-id="'+postId+'"]').removeClass('active');
					likeCount = parseInt($('.like .like-count').html()) - 1;
					$('.like .like-count').html(likeCount);
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log('Error: '+ errorThrown);
			}
		});
	}
});