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
	$('.like-brick').on('click', '.like', function () {
		let postId = $(this).attr('data-post-id');
		let doAction = '';

		if ($(this).hasClass('active')) {
			doAction = 'delete';
		} else {
			doAction = 'add';
		}
		addLike(postId, doAction);
	});

	function addLike(postId, action) {
		let param = 'postId='+postId+"&action="+action;

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

					$('.head_like_cnt').html(likeCount); // Лайк в шапке статьи
					$('.like .like-count').html(likeCount); // Лайк в футере статьи
				}

				if (result == 2) {
					// Всплывашка - вы уже поставили лайк
					$('.like[data-post-id="'+postId+'"]').removeClass('active');
					$('.bubble[data-id="'+postId+'"]').addClass('active');
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log('Error: '+ errorThrown);
			}
		});
	}

	$('.bubble').on('click', '.tfl-popup__close', function () {
		$(this).parent().removeClass('active');
	});

	$('.share-brick').on('click', '.copy-url', function (e) {
		e.preventDefault();
		var url = window.location.href;
		var copyTextarea = document.createElement("textarea");
		copyTextarea.style.position = "fixed";
		copyTextarea.style.opacity = "0";
		copyTextarea.textContent = url;

		document.body.appendChild(copyTextarea);
		copyTextarea.select();
		document.execCommand("copy");
		document.body.removeChild(copyTextarea);
	});
});