function isEmpty(str) {
    return (!str || 0 === str.length);
}

function autocomplete(qid) {
	var questionContainer = $('#question' + qid),
		answerContainer = questionContainer.find('.answer-item'),
		otherInput = answerContainer.find('input[id^="othertext"]'),
		selectInput = answerContainer.find('select.list-question-select'),
		autocompleteInput = $('<input type="text" data-provide="typeahead" autocomplete="off">'),

		suggestions = function(query, process) {
	        return $.getJSON(window.location, {
					qid: qid, 
					query: query
				}, process);
		};

	autocompleteInput.attr('id', 'autocomplete' + qid);
	autocompleteInput.attr('class', otherInput.attr('class'));
	questionContainer.find('p:contains("please also specify your choice")').hide();
	answerContainer.find('*').hide();
	answerContainer.append(autocompleteInput);

	autocompleteInput.typeahead({
		items: 'all',
		minLength: 2,
		delay: 200,
		fitToElement: true,
		source: suggestions
	});

	autocompleteInput.change(function() {
		var givenOption = autocompleteInput.val(),
		    suggestion = autocompleteInput.typeahead("getActive");

		if (isEmpty(givenOption)) {
			selectInput.val('');
			otherInput.val('');

		} else if (suggestion && suggestion.name == givenOption) {
			selectInput.val(suggestion.id);
			otherInput.val('');

		} else {
			selectInput.val('-oth-');
			otherInput.val(givenOption);
		}
	});

}