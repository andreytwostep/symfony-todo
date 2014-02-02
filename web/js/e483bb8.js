// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.

$(function() {
	// complete selected task
	$(document).on('click', '.ajax-task-complete', function(e) {
		e.preventDefault();
		var href = $(this).attr('href');
		var title = $(this).closest('li.task-item').find('[class^=task][class$=done]');

		$('<div></div>').load(href + ' form', function() {
			var form = $(this).children('form');
			var checkBox = form.find('input[type=checkbox]');
			checkBox.prop('checked', !checkBox.prop('checked'));

			$.ajax({
				url: form.attr('action'),
				data: form.serialize(),
				method: 'post',
				dataType: 'json',
				cache: false,
				success: function(obj) {
					var clk = $('#task-complete-' + obj.id + ' .ajax-task-complete');
					var completed = parseInt($('#completed').text());
					var left = parseInt($('#counter').text());
					var dataIds = $('#clear-completed').attr('data-ids');
					var ids = dataIds.split(',');
					var newId = obj.id.toString();
					if(obj.complete) {
						clk.text('☑');
						title.toggleClass('task-done task-undone');

						$('#completed').text(completed+1);
						$('#counter').text(left-1);

						ids = (dataIds.length) ? dataIds.split(',') : [] ;
						if(ids.indexOf(newId) == -1) {
							ids.push(newId);
							$('#clear-completed').attr('data-ids', ids.toString());
						}
					}
					else {
						clk.text('☐');
						title.toggleClass('task-undone task-done');

						$('#completed').text(completed-1);
						$('#counter').text(left+1);

						if(ids.indexOf(newId) != -1) {
							ids.splice(ids.indexOf(newId),1);
							$('#clear-completed').attr('data-ids', ids.toString());
						}
					}
				},
				complete: function() {
					console.log('complete!');
				},
				error: function() {
					console.log('error');
				}
			})
		})
	}
	)

	// create new task
	$('#main-create form:first-child').submit( function(e) {
		e.preventDefault();
		var form = $(this).closest('form');
		var url = form.attr('action');
		var left = parseInt($('#counter').text());

		$.ajax({
			url: url,
			data: form.serialize(),
			method: 'post',
			dataType: 'json',
			cache: false,
			success: function(obj) {
				$('ul.records_list .task-create').after(
					'<li class="task-item">'+
						'<div id="task-complete-'+ obj.id +'">' +
							'<a class="ajax-task-complete" href="/symfony/web/task/'+ obj.id +'/edit"> ☐ </a>' +
						'</div>' +
						'<div class="task-elem">' +
							'<div class="task-undone">' +
								'<a class="ajax-task-edit" href="/symfony/web/task/'+ obj.id +'/edit">'+ obj.task +'</a>' +
								'<input class="edit-task" type="text" value="'+ obj.task +'">' +
							'</div>' +
							'<div class="ajax-task-delete" href="/symfony/web/task/'+ obj.id +'" style="display: none;"></div>' +
						'</div>' +
					'</li>'
				);
				form.find('input[type="text"]').val('');
				$('#counter').text(left+1);
			},
			complete: function() {
				console.log('complete!');
			},
			error: function() {
				console.log('error');
			}
		})
		}
	)

	// show/hide delete button
	$(document).on({
		mouseenter: function () {
			$(this).find('.ajax-task-delete').show();
		},
		mouseleave: function () {
			$(this).find('.ajax-task-delete').hide();
		}
	}, '.task-item');


	// delete selected task
	$(document).on('click', '.ajax-task-delete', function(e) {
			e.preventDefault();
			var href = $(this).attr('href');
			var me = $(this);
			var title = $(this).closest('li.task-item').find('[class^=task][class$=done]');
			var completed = parseInt($('#completed').text());
			var left = parseInt($('#counter').text());

			var curId = $(this).closest('li.task-item').find('[id^=task-complete]').attr('id').split('task-complete-')[1];

			$('<div></div>').load(href + ' form', function() {
				var form = $(this).children('form');
				$.ajax({
					url: form.attr('action'),
					data: form.serialize(),
					method: 'post',
					dataType: 'json',
					cache: false,
					success: function(obj) {

						var dataIds = $('#clear-completed').attr('data-ids');
						var ids = (dataIds.length) ? dataIds.split(',') : [] ;
						if(ids.indexOf(curId) != -1) {
							ids.splice(ids.indexOf(curId),1);
							$('#clear-completed').attr('data-ids', ids.toString());
						}

						if(title.attr('class') == 'task-done') {
							$('#completed').text(completed-1);
						} else {
							$('#counter').text(left-1);
						}
						me.closest('li.task-item').remove();
					},
					complete: function() {
						console.log('complete!');
					},
					error: function() {
						console.log('error');
					}
				})
			})
		}
	);

	// edit selected task
	$(document).on('dblclick', '.ajax-task-edit', function(e) {
		e.preventDefault();
		var href = $(this).attr('href');
		$(this).hide();
		$(this).next().show();

		$(document).on('keypress', '.edit-task', function(e) {
			if (e.keyCode == 13) {
				var editField = $(this);
				var newValue = editField.val();

				$('<div></div>').load(href + ' form', function() {
					var form = $(this).children('form');
					var text = form.find('input[type=text]');
					newValue.length ? text.val(newValue) : text.val();

					$.ajax({
						url: form.attr('action'),
						data: form.serialize(),
						method: 'post',
						dataType: 'json',
						cache: false,
						success: function(obj) {
							editField.hide();
							if(newValue.length)
								editField.prev().text(newValue).show();
							else
								editField.prev().show();
						},
						complete: function() {
							console.log('complete!');
						},
						error: function() {
							console.log('error');
						}
					})
				})
				return false;
			}
		});
		}
	);

	$(document).on('click', '.ajax-task-edit', function(e) {
		e.preventDefault();
	});

	// delete all completed tasks
	$(document).on('click', '#clear-completed', function(e) {
			e.preventDefault();

			if($(this).attr('data-ids').length > 0) {
				var me = $(this);
				var urlstr = $(this).attr('href').split('/0')[0];
				var href = urlstr + '/' + $(this).attr('data-ids');

				$('<div></div>').load(href + ' form', function() {
					var form = $(this).children('form');

					$.ajax({
						url: href,
						data: form.serialize(),
						method: 'post',
						dataType: 'json',
						cache: false,
						success: function(obj) {
							var completed = parseInt($('#completed').text());
							var ids = obj.id.split(',');
							ids.forEach(function(entry) {
								$('#task-complete-' +  entry).closest('li.task-item').remove();
							});
							me.attr('data-ids', '');
							$('#completed').text(completed-ids.length);
						},
						complete: function() {
							console.log('complete!');
						},
						error: function() {
							console.log('error');
						}
					})
				})
			}
		}
	)

})
