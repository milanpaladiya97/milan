(function ($) {
    'use strict';
    $.fn.serializeFormJSON = function () {
        var o = {},
            a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };    
    if (!String.prototype.trim) {
        (function() {
            String.prototype.trim = function() {
                return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
            };
        })();
    }
    $(document).ready(function () {


        var current_fs, next_fs, previous_fs; //fieldsets
        var left, opacity, scale; //fieldset properties which we will animate
        var animating; //flag to prevent quick multi-click glitches
        var form, ays_quiz_container, ays_quiz_container_id;
        if(!$.fn.goTo){
            $.fn.goTo = function() {
                $('html, body').animate({
                    scrollTop: $(this).offset().top - 100 + 'px'
                }, 'slow');
                return this; // for chaining...
            }
        }
        if (!String.prototype.trim) {
            (function() {
                String.prototype.trim = function() {
                    return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                };
            })();
        }
        $(document).find(".ays-quiz-container .information_form").each(function(e){
            var $this = $(this);
            var cont = $(document).find(".ays-quiz-container");
            var thisCont = $this.parents('.ays-quiz-container');
            var quizId = thisCont.find('input[name="ays_quiz_id"]').val();
            var myOptions = JSON.parse(window.atob(window.aysQuizOptions[quizId]));
            if(myOptions.autofill_user_data && myOptions.autofill_user_data == "on"){
                var userData = {};
                userData.action = 'ays_get_user_information';
                $.ajax({
                    url: quiz_maker_ajax_public.ajax_url,
                    method: 'post',
                    dataType: 'json',
                    data: userData,
                    success: function (response) {
                        if(response !== null){
                            $this.find("input[name='ays_user_name']").val(response.data.display_name);
                            $this.find("input[name='ays_user_email']").val(response.data.user_email);
                        }
                    }
                });
            }
        });
        $(document).find('input.ays_finish').on('click', function (e) {
            e.preventDefault();
            ays_quiz_container_id = $(this).parents(".ays-quiz-container").attr("id");
            ays_quiz_container = $('#'+ays_quiz_container_id);
            if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top){
                ays_quiz_container.goTo();
            }
            if(ays_quiz_container.find('.ays_music_sound').length !== 0){
                ays_quiz_container.find('.ays_music_sound').fadeOut();
                setTimeout(function() {
                    audioVolumeOut(ays_quiz_container.find('.ays_quiz_music').get(0));
                },4000);
                setTimeout(function() {
                    ays_quiz_container.find('.ays_quiz_music').get(0).pause();
                },6000);
            }
            if(ays_quiz_container.find('audio').length > 0){
                ays_quiz_container.find('audio').each(function(e, el){
                    el.pause();
                });
            }
            if(ays_quiz_container.find('video').length > 0){
                ays_quiz_container.find('video').each(function(e, el){
                    el.pause();
                });
            }
            ays_quiz_container.find('.ays-live-bar-wrap').addClass('bounceOut');            
            setTimeout(function () {
                ays_quiz_container.find('.ays-live-bar-wrap').css('display','none');
            },300);
            var quizId = ays_quiz_container.find('input[name="ays_quiz_id"]').val();
            var myOptions = JSON.parse(window.atob(window.aysQuizOptions[quizId]));
            var quizOptionsName = 'quizOptions_'+quizId;
            var myQuizOptions = [];            
            
            if(typeof window[quizOptionsName] !== 'undefined'){
                for(var i in window[quizOptionsName]){
                    myQuizOptions[i] = (JSON.parse(window.atob(window[quizOptionsName][i])));
                }
            }
            
            var emailValivatePattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/;

            if (!($(this).hasClass('start_button'))) {
                if ($(this).parents('.step').find('input[required]').length !== 0) {
                    var empty_inputs = 0;
                    var required_inputs = $(this).parents('.step').find('input[required]');
                    $(this).parents('.step').find('.ays_red_border').removeClass('ays_red_border');
                    $(this).parents('.step').find('.ays_green_border').removeClass('ays_green_border');
                    for (var i = 0; i < required_inputs.length; i++) {
                        
                        switch(required_inputs.eq(i).attr('name')){
                            case "ays_user_phone": {
                                if (!validatePhoneNumber(required_inputs.eq(i).get(0))) {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }
                                break;
                            }
                            case "ays_user_email": {
                                if (!(emailValivatePattern.test(required_inputs.eq(i).val()))) {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }
                                break;
                            }
                            default:{
                                if (required_inputs.eq(i).val() === '' &&
                                    required_inputs.eq(i).attr('type') !== 'hidden') {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }
                                break;
                            }
                        }
                    }
                    var empty_inputs2 = 0;
                    var phoneInput = $(this).parents('.step').find('input[name="ays_user_phone"]');
                    var emailInput = $(this).parents('.step').find('input[name="ays_user_email"]');
                    if(phoneInput.val() != ''){
                        phoneInput.removeClass('ays_red_border');
                        phoneInput.removeClass('ays_green_border');
                        if (!validatePhoneNumber(phoneInput.get(0))) {
                            if (phoneInput.attr('type') !== 'hidden') {
                                phoneInput.addClass('ays_red_border');
                                phoneInput.addClass('shake');
                                empty_inputs2++;
                            }
                        }else{
                            phoneInput.addClass('ays_green_border');
                        }
                    }
                    if(emailInput.val() != ''){
                        emailInput.removeClass('ays_red_border');
                        emailInput.removeClass('ays_green_border');
                        if (!(emailValivatePattern.test(emailInput.val()))) {
                            if (emailInput.attr('type') !== 'hidden') {
                                emailInput.addClass('ays_red_border');
                                emailInput.addClass('shake');
                                empty_inputs2++;
                            }
                        }else{
                            emailInput.addClass('ays_green_border');
                        }
                    }
                    var errorFields = $(this).parents('.step').find('.ays_red_border');
                    if (empty_inputs2 !== 0 || empty_inputs !== 0) {
                        setTimeout(function(){
                            errorFields.each(function(){
                                $(this).removeClass('shake');
                            });
                        }, 500);
                        setTimeout(function(){
                            required_inputs.each(function(){
                                $(this).removeClass('shake');
                            });
                        }, 500);
                        return false;
                    }
                }else{
                    if($(this).parents('.step').find('.information_form').length !== 0 ){
                        var empty_inputs = 0;
                        var phoneInput = $(this).parents('.step').find('input[name="ays_user_phone"]');
                        var emailInput = $(this).parents('.step').find('input[name="ays_user_email"]');
                        if(phoneInput.val() != ''){
                            phoneInput.removeClass('ays_red_border');
                            phoneInput.removeClass('ays_green_border');
                            if (!validatePhoneNumber(phoneInput.get(0))){
                                if (phoneInput.attr('type') !== 'hidden') {
                                    phoneInput.addClass('ays_red_border');
                                    phoneInput.addClass('shake');
                                    empty_inputs++;
                                }
                            }else{
                                phoneInput.addClass('ays_green_border');
                            }
                        }
                        if(emailInput.val() != ''){
                            emailInput.removeClass('ays_red_border');
                            emailInput.removeClass('ays_green_border');
                            if (!(emailValivatePattern.test(emailInput.val()))) {
                                if (emailInput.attr('type') !== 'hidden') {
                                    emailInput.addClass('ays_red_border');
                                    emailInput.addClass('shake');
                                    empty_inputs++;
                                }
                            }else{
                                emailInput.addClass('ays_green_border');
                            }
                        }
                        var errorFields = $(this).parents('.step').find('.ays_red_border');
                        if (empty_inputs !== 0) {
                            setTimeout(function(){
                                errorFields.each(function(){
                                    $(this).removeClass('shake');
                                });
                            }, 500);
                            return false;
                        }
                    }
                }
            }

            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();
            $(e.target).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', false);
            $(e.target).parents().eq(3).find('div.ays-quiz-timer').slideUp(500);
            if($(e.target).parents().eq(3).find('div.ays-quiz-after-timer').hasClass('empty_after_timer_text')){
                $(e.target).parents().eq(3).find('div.ays-quiz-timer').parent().slideUp(500);
            }
            // setTimeout(function () {
            //     $(e.target).parents().eq(3).find('div.ays-quiz-timer').parent().hide();
            // },500);

            next_fs = $(this).parents('.step').next();
            current_fs = $(this).parents('.step');
            next_fs.addClass('active-step');
            current_fs.removeClass('active-step');
            form = ays_quiz_container.find('form');
            
            var textAnswers = form.find('div.ays-text-field textarea.ays-text-input');            
            for(var i=0; i < textAnswers.length; i++){
                var userAnsweredText = textAnswers.eq(i).val().trim();
                var questionId = textAnswers.eq(i).parents('.step').data('questionId');
                
                var trueAnswered = false;
                
                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;
                var thisQuestionAnswer = thisQuestionCorrectAnswer.toLowerCase();
                thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                for(var i_answer = 0; i_answer < thisQuestionAnswer.length; i_answer++){
                    if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i_answer].trim()){
                        trueAnswered = true;
                        break;
                    }
                }
                
                if(trueAnswered){
                    textAnswers.eq(i).next().val(1);
                }else{
                    textAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        textAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        textAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }
                textAnswers.eq(i).removeAttr('disabled');
            }
            
            var numberAnswers = form.find('div.ays-text-field input[type="number"].ays-text-input');            
            for(var i=0; i < numberAnswers.length; i++){
                var userAnsweredText = numberAnswers.eq(i).val().trim();
                var questionId = numberAnswers.eq(i).parents('.step').data('questionId');
                if(userAnsweredText.toLowerCase() === myQuizOptions[questionId].question_answer.toLowerCase()){
                    numberAnswers.eq(i).next().val(1);
                }else{
                    numberAnswers.eq(i).next().val(0);
                    numberAnswers.eq(i).attr('chishtpatasxan', myQuizOptions[questionId].question_answer);                    
                }
                numberAnswers.eq(i).removeAttr('disabled')
            }
            
            var shortTextAnswers = form.find('div.ays-text-field input[type="text"].ays-text-input');            
            for(var i=0; i < shortTextAnswers.length; i++){
                var userAnsweredText = shortTextAnswers.eq(i).val().trim();
                var questionId = shortTextAnswers.eq(i).parents('.step').data('questionId');
                
                var trueAnswered = false;
                
                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;
                var thisQuestionAnswer = thisQuestionCorrectAnswer.toLowerCase();
                thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                for(var i_answer = 0; i_answer < thisQuestionAnswer.length; i_answer++){
                    if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i_answer].trim()){
                        trueAnswered = true;
                        break;
                    }
                }
                
                if(trueAnswered){
                    shortTextAnswers.eq(i).next().val(1);
                }else{
                    shortTextAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        shortTextAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        shortTextAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }
                
                shortTextAnswers.eq(i).removeAttr('disabled')
            }
            
            var dateAnswers = form.find('div.ays-text-field input[type="date"].ays-text-input');
            for(var i=0; i < dateAnswers.length; i++){
                var userAnsweredText = dateAnswers.eq(i).val();
                var questionId = dateAnswers.eq(i).parents('.step').data('questionId');
                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;
                
                var trueAnswered = false;
                var correctDate = new Date(thisQuestionCorrectAnswer),
                    correctDateYear = correctDate.getFullYear(),
                    correctDateMonth = correctDate.getMonth(),
                    correctDateDay = correctDate.getDate();
                var userDate = new Date(userAnsweredText),
                    userDateYear = userDate.getFullYear(),
                    userDateMonth = userDate.getMonth(),
                    userDateDay = userDate.getDate();

                if(correctDateYear == userDateYear && correctDateMonth == userDateMonth && correctDateDay == userDateDay){
                    trueAnswered = true;
                }
                
                if(trueAnswered){
                    dateAnswers.eq(i).next().val(1);
                }else{
                    dateAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        dateAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        dateAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }
                
                dateAnswers.eq(i).removeAttr('disabled')
            }
            
            var data = form.serializeFormJSON();

            var questionsIds = data.ays_quiz_questions.split(',');
            for(var i = 0; i < questionsIds.length; i++){
                if(! isNaN(parseInt(questionsIds[i]))){
                    if(! data['ays_questions[ays-question-'+questionsIds[i]+']']){
                        data['ays_questions[ays-question-'+questionsIds[i]+']'] = "";
                    }
                }
            }

            data.action = 'ays_finish_quiz';
            data.end_date = GetFullDateTime();
            
            var aysQuizLoader = form.find('div[data-role="loader"]');
            aysQuizLoader.addClass(aysQuizLoader.data('class'));
            aysQuizLoader.removeClass('ays-loader');

            var animationOptions = {
                scale: scale,
                left: left,
                opacity: opacity,
                animating: animating
            }
            
            setTimeout(function () {
                sendQuizData(data, form, myOptions, animationOptions, $(e.target));
            },2000);

            if (parseInt(next_sibilings_count) > 0 && ($(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id'))) {
                current_fs = $(this).parents('form').find('div[data-question-id]');
            }
            
            // aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);
        });
    });
    
    function sendQuizData(data, form, myOptions, options, element){
        if(typeof sendQuizData.counter == 'undefined'){
            sendQuizData.counter = 0;
        }
        if(window.navigator.onLine){
            sendQuizData.counter++;
            $.ajax({
                url: window.quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.status === true){
                        doQuizResult(response, form, myOptions);
                    }else{
                        if(sendQuizData.counter >= 5){
                            swal.fire({
                                type: 'error',
                                html: "Sorry.<br>We are unable to store your data."
                            });
                            goQuizFinishPage(form, options, element, myOptions);
                        }else{
                            if(window.navigator.onLine){
                                setTimeout(function(){
                                    sendQuizData(data, form, myOptions, options, element);
                                },3000);
                            }else{
                                sendQuizData(data, form, myOptions, options, element);
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if(sendQuizData.counter >= 5){
                        swal.fire({
                            type: 'error',
                            html: "Sorry.<br>We are unable to store your data."
                        });
                        goQuizFinishPage(form, options, element, myOptions);
                    }else{
                        setTimeout(function(){
                            sendQuizData(data, form, myOptions, options, element);
                        },3000);
                    }
                }
            });
        }else{
            swal.fire({
                type: 'warning',
                html: "Connection is lost.<br>Please check your connection and try again."
            });
            sendQuizData.counter = 0;
            goQuizFinishPage(form, options, element, myOptions);
        }
    }
    
    function goQuizFinishPage(form, options, element, myOptions){        
        var currentFS = form.find('.step.active-step');        
        var next_sibilings_count = form.find('.ays_question_count_per_page').val();
        if (parseInt(next_sibilings_count) > 0 &&
            (element.parents('.step').attr('data-question-id') ||
             element.parents('.step').next().attr('data-question-id'))) {
            currentFS = form.find('div[data-question-id]');
        }
        currentFS.prev().css('display', 'flex');
        currentFS.animate({opacity: 0}, {
            step: function(now, mx) {
                options.scale = 1 - (1 - now) * 0.2;
                options.left = (now * 50)+"%";
                options.opacity = 1 - now;
                currentFS.css({
                    'transform': 'scale('+options.scale+')',
                    'position': '',
                    'pointer-events': 'none'
                });
                currentFS.prev().css({
                    'left': options.left,
                    'opacity': options.opacity,
                    'pointer-events': 'none'
                });
            },
            duration: 800,
            complete: function(){
                currentFS.hide();
                currentFS.css({
                    'pointer-events': 'auto',
                    'opacity': '1',
                });
                currentFS.prev().css({
                    'transform': 'scale(1)',
                    'position': 'relative',
                    'opacity': '1',
                    'pointer-events': 'auto'
                });
                options.animating = false;
            },
            easing: 'easeInOutBack'
        });
        if(myOptions.enable_correction == 'on'){
            if(currentFS.prev().find('input:checked').length > 0){
                currentFS.prev().find('.ays-field input').attr('disabled', 'disabled');
                currentFS.prev().find('.ays-field input').on('click', function(){
                    return false;
                });
                currentFS.prev().find('.ays-field input').on('change', function(){
                    return false;
                });
            }
            if(currentFS.prev().find('option:checked').length > 0){
                currentFS.prev().find('.ays-field select').attr('disabled', 'disabled');
                currentFS.prev().find('.ays-field select').on('click', function(){
                    return false;
                });
                currentFS.prev().find('.ays-field select').on('change', function(){
                    return false;
                });
            }
            if(currentFS.prev().find('textarea').length > 0){
                if(currentFS.prev().find('textarea').val() !== ''){
                    currentFS.prev().find('.ays-field textarea').attr('disabled', 'disabled');
                    currentFS.prev().find('.ays-field textarea').on('click', function(){
                        return false;
                    });
                    currentFS.prev().find('.ays-field textarea').on('change', function(){
                        return false;
                    });
                }
            }
        }
    }
    
    function doQuizResult(response, form, myOptions){
        $('.subscribe_title').addClass('display_none');
		var hideQuizBGImage = form.parents('.ays-quiz-container').data('hideBgImage');
		var QuizBGGragient = form.parents('.ays-quiz-container').data('bgGradient');
		if(hideQuizBGImage){
			form.parents('.ays-quiz-container').css('background-image', 'none');
			if(typeof QuizBGGragient != 'undefined'){
				form.parents('.ays-quiz-container').css('background-image', QuizBGGragient);
			}
		}
        form.find('div.ays_message').css('display', 'none');
        form.find('.ays_average').css({'display': 'block'});
        var quizScore = '';
        switch(response.displayScore){
            case 'by_percantage':
                quizScore = parseInt(response.score);
            break;
            case 'by_correctness':
                quizScore = response.score.split('/');
            break;
        }
        if (response.hide_result) {
            form.find('div.ays_message').html(response.text);
        }
        else {
            form.find('div.ays_message').html(response.text);
            form.find('p.ays_score').removeClass('ays_score_display_none');

             var my_score = response.score.replace('%', '');

              
             if(response.score == '100%'){
                 form.find('p.ays_score').html(form.find('p.ays_score').text()+'<span class="ays_score_percent animated green">Excellent</span>');
                form.find('#matrix_excellent').addClass('display_block');

                form.find('#matrix_good').addClass('display_none');
                form.find('#matrix_poor').addClass('display_none');
                form.find('#matrix_average').addClass('display_none');


                form.find('.ays-progress-value').addClass('green');
                form.find('.ays-progress-value').removeClass('orange');
                form.find('.ays-progress-value').removeClass('red');
                }

           else if(response.score >= '0%' && response.score <= '40%'){
                 form.find('p.ays_score').html(form.find('p.ays_score').text()+'<span class="ays_score_percent animated red">Poor</span>');
                form.find('#matrix_poor').addClass('display_block');

                 form.find('#matrix_excellent').addClass('display_none');
                form.find('#matrix_good').addClass('display_none');
                form.find('#matrix_average').addClass('display_none');

                 form.find('.ays-progress-value').addClass('red');
                 form.find('.ays-progress-value').removeClass('green');
                 form.find('.ays-progress-value').removeClass('orange');

                }
                else if(response.score > '40%' && response.score <= '70%'){
                 form.find('p.ays_score').html(form.find('p.ays_score').text()+'<span class="ays_score_percent animated orange">Average</span>');

                form.find('#matrix_average').addClass('display_block');

                form.find('#matrix_good').addClass('display_none');
                 form.find('#matrix_poor').addClass('display_none');
                form.find('#matrix_excellent').addClass('display_none');

                form.find('.ays-progress-value').addClass('orange');
                 form.find('.ays-progress-value').removeClass('red');
                 form.find('.ays-progress-value').removeClass('green');
              
                }
                else if(response.score > '70%' && response.score <= '90%'){
                 form.find('p.ays_score').html(form.find('p.ays_score').text()+'<span class="ays_score_percent animated green">Good</span>');

                 form.find('#matrix_good').addClass('display_block');
                
                form.find('#matrix_poor').addClass('display_none');
                form.find('#matrix_excellent').addClass('display_none');
                form.find('#matrix_average').addClass('display_none');

                form.find('.ays-progress-value').addClass('green');
                form.find('.ays-progress-value').removeClass('orange');
                 form.find('.ays-progress-value').removeClass('red');
             
                }
                
              
                


                 // form.find('p.ays_score').html(form.find('p.ays_score').text()+'<span class="ays_score_percent animated"> ' + response.score + "/ 100" + '</span>');
            
        }
        form.find('div.ays_message').fadeIn(500);
        setTimeout(function () {
            form.find('p.ays_score').addClass('tada');



        }, 500);
        var numberOfPercent = 0;
        var percentAnimate = setInterval(function(){
            if(typeof quizScore == 'number'){

                form.find('.ays-progress-value').text(numberOfPercent + " / 100");
                if(numberOfPercent == quizScore){
                    clearInterval(percentAnimate);
                }
                numberOfPercent++;
            }else{
                var total = quizScore[1];
                var count = quizScore[0];
                total = parseInt(total.trim());
                count = parseInt(count.trim());
                form.find('.ays-progress-value').text(numberOfPercent + " / " + total);
                if(numberOfPercent == count){
                    clearInterval(percentAnimate);
                }
                numberOfPercent++;
            }
        }, 20);
        var score = quizScore;
        if(response.displayScore == 'by_correctness'){
            var total = parseInt(quizScore[1].trim());
            var count = parseInt(quizScore[0].trim());
            score = (count / total) * 100;
        }

        if(score > 0){
           

            form.find('.ays-progress-bar').css('padding-right', '7px');
            var progressBarStyle = myOptions.progress_bar_style ? myOptions.progress_bar_style : 'first';
            if(progressBarStyle == 'first' || progressBarStyle == 'second'){
                form.find('.ays-progress-value').css('width', 0);
                form.find('.ays-progress-value').css('transition', 'width ' + score*25 + 'ms linear');
                setTimeout(function(){
                    form.find('.ays-progress-value').css('width', score+'%');
                }, 1);
            }
            form.find('.ays-progress-bar').css('transition', 'width ' + score*25 + 'ms linear');
            setTimeout(function(){
                form.find('.ays-progress-bar').css('width', score+'%');
            }, 1);
        }

        form.append($("<div class='ays_quiz_results'></div>"));
        var formResults = form.find('.ays_quiz_results');
        formResults.css('padding-bottom', '20px');
        if (form.hasClass('enable_questions_result')) {
            var questions = form.find('div[data-question-id]');
            for (var z = 0; z < questions.length; z++) {
                
                if(questions.eq(z).hasClass('not_influence_to_score')){
                    continue;
                }
                var question = questions.eq(z).clone();
                question.find('input[type="button"]').remove();
                question.find('input[type="submit"]').remove();
                question.find('.ays_arrow').remove();
                question.addClass('ays_question_result');
                var checked_inputs = question.find('input:checked');
                var text_answer = question.find('textarea.ays-text-input');
                var number_answer = question.find('input[type="number"].ays-text-input');
                var short_text_answer = question.find('input[type="text"].ays-text-input');
                var date_answer = question.find('input[type="date"].ays-text-input');
                var selected_options = question.find('select');
                var answerIsRight = false;                
                
                question.find('input[name="ays_answer_correct[]"][value="1"]').parent().find('label').addClass('correct answered');
                question.find('input[name="ays_answer_correct[]"][value="1"]').parents('div.ays-field').addClass('correct_div');
                
                if(checked_inputs.length === 0){
                    var emptyAnswer = false;
                    if(question.find('input[type="radio"]').length !== 0 ||
                       question.find('input[type="checkbox"]').length !== 0){
                        emptyAnswer = true;
                    }
                    if(emptyAnswer){
                        question.find('.ays-abs-fs').html("<fieldset class='ays_fieldset'>"+
                                "<legend>" + quizLangObj.notAnsweredText + "</legend>"+
                                question.find('.ays-abs-fs').html()+
                          "</fieldset>");
                        question.find('.ays-abs-fs').css({
                            'padding': '7px'
                        });
                    }
                }

                var aysAudio = $(document).find('.ays_question_result audio');
                if(aysAudio.length > 0){
                    aysAudio.each(function(e, el){
                        el.pause();
                    });
                }

                selected_options.each(function(element, item){
                    var selectOptions = $(item).children("option[data-chisht]");
                    var answerClass, answerDivClass, attrChecked, answerClassForSelected, answerClass_tpel, answerViewClass, attrCheckedStyle = "";
                    var correctAnswersDiv = '', rectAnswerBefore = "";
                    
                    
                    answerViewClass = form.find('.answer_view_class').val();
                    answerViewClass = "ays_"+form.find('.answer_view_class').val()+"_view_item";
                    for(var j = 0; j < selectOptions.length; j++){
                        if($(selectOptions[j]).attr("value") == '' ||
                           $(selectOptions[j]).attr("value") == undefined ||
                           $(selectOptions[j]).attr("value") == null){
                            continue;
                        }
                        if($(selectOptions[j]).prop('selected') == true){
                            if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                answerClassForSelected = " correct answered";
                                answerDivClass = "correct_div";
                                attrChecked = "checked='checked'";
                                answerIsRight = true;
                            }else{
                                answerClassForSelected = " wrong wrong_div answered";
                                attrChecked = "checked='checked'";
                                answerDivClass = "";
                            }
                        }else{
                            if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                answerClassForSelected = " correct answered";
                                answerDivClass = "correct_div";
                                attrChecked = "";
                            }else{
                                answerClassForSelected = "";
                                attrChecked = "";
                                answerDivClass = "";
                            }
                        }
                        if(form.parents('.ays-quiz-container').hasClass('ays_quiz_elegant_dark') ||
                           form.parents('.ays-quiz-container').hasClass('ays_quiz_elegant_light')){
                            if($(selectOptions[j]).prop('selected') == true){
                                if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                    answerDivClass = "correct_div checked_answer_div";
                                    attrCheckedStyle = "style='padding: 0!important;'";
                                }else{
                                    answerDivClass = "wrong_div checked_answer_div";
                                    attrCheckedStyle = "style='padding: 0!important;'";
                                }
                            }else{
                                if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                    answerDivClass = "correct_div";
                                }else{
                                    answerDivClass = "";
                                }
                                attrCheckedStyle = "";
                            }
                        }
                        if(form.parents('.ays-quiz-container').hasClass('ays_quiz_rect_dark') ||
                           form.parents('.ays-quiz-container').hasClass('ays_quiz_rect_light')){
                            if($(selectOptions[j]).prop('selected') == true){
                                if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                    answerDivClass = "correct_div checked_answer_div";
                                }else{
                                    answerDivClass = "wrong_div checked_answer_div";
                                }
                                rectAnswerBefore = "rect_answer_correct_before";
                            }else{
                                if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                    answerDivClass = "correct_div";
                                }else{
                                    answerDivClass = "";
                                }
                                rectAnswerBefore = "rect_answer_wrong_before";
                            }
                        }
                        correctAnswersDiv += '<div class="ays-field '+answerViewClass+' '+answerDivClass+'" '+attrCheckedStyle+'>'+
                                '<input type="radio" value="'+$(selectOptions[j]).attr("value")+'" name="'+$(item).parent().find('.ays-select-field-value').attr('name')+'" disabled="disabled" '+attrChecked+'>'+
                                '<label class="'+rectAnswerBefore+" "+answerClassForSelected+'" for="ays-answer-'+$(selectOptions[j]).attr("value")+'">'+aysEscapeHtml($(selectOptions[j]).text())+'</label>'+
                            '</div>';
                    }
                    $(item).parent().parent().find('.ays-text-right-answer').remove();
                    $(item).parent().parent().append(correctAnswersDiv);
                    $(item).parent().hide();
                    if($(item).find('option[data-chisht]:selected').length === 0){
                        $(item).parents('.ays-abs-fs').html("<fieldset class='ays_fieldset'>"+
                                "<legend>" + quizLangObj.notAnsweredText + "</legend>"+
                                $(item).parents('.ays-abs-fs').html()+
                          "</fieldset>");
                        $(item).parents('.ays-abs-fs').css({
                            'padding': '7px'
                        })
                    }
                    $(item).parents('.ays-abs-fs').find('.ays_buttons_div').remove();
                    $(item).parent().remove();
                });

                text_answer.next().next().remove();
                text_answer.css('width', '100%');
                text_answer.attr('disabled', 'disabled');
                number_answer.next().next().remove();
                number_answer.css('width', '100%');
                number_answer.attr('disabled', 'disabled');
                short_text_answer.next().next().remove();
                short_text_answer.css('width', '100%');
                short_text_answer.attr('disabled', 'disabled');
                date_answer.next().next().remove();
                date_answer.css('width', '100%');
                date_answer.attr('disabled', 'disabled');
                if(text_answer.val() == ''){
                    var rightAnswerText = '<div class="ays-text-right-answer">';
                    var thisQuestionAnswer = text_answer.attr('chishtpatasxan');
                    if(typeof thisQuestionAnswer != 'undefined'){
                        thisQuestionAnswer = thisQuestionAnswer.toLowerCase().split('%%%');
                    }else{
                        thisQuestionAnswer = [''];
                    }
                    rightAnswerText += thisQuestionAnswer[0].trim();
                    
                    rightAnswerText += '</div>';
                    if(text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                        text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                    }
                    text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').show();
                    text_answer.css('background-color', 'rgba(243,134,129,0.4)');
                    text_answer.parents('.ays-abs-fs').html("<fieldset class='ays_fieldset'>"+
                            "<legend>" + quizLangObj.notAnsweredText + "</legend>"+
                            text_answer.parents('.ays-abs-fs').html()+
                      "</fieldset>");
                    text_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(text_answer.next().val()) == 1){
                        text_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;
                    }else{
                        text_answer.css('background-color', 'rgba(243,134,129,0.4)');
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = text_answer.attr('chishtpatasxan');
                        if(typeof thisQuestionAnswer != 'undefined'){
                            thisQuestionAnswer = thisQuestionAnswer.toLowerCase().split('%%%');
                        }else{
                            thisQuestionAnswer = [''];
                        }
                        rightAnswerText += thisQuestionAnswer[0].trim();

                        rightAnswerText += '</div>';                            
                        if(text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').slideDown(500);
                    }
                }
                if(number_answer.val() == ''){
                    var rightAnswerText = '<div class="ays-text-right-answer">'+
                        number_answer.attr('chishtpatasxan')+
                    '</div>';
                    if(number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                        number_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                    }
                    number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').show();
                    number_answer.css('background-color', 'rgba(243,134,129,0.8)');
                    number_answer.parents('.ays-abs-fs').html("<fieldset class='ays_fieldset'>"+
                            "<legend>" + quizLangObj.notAnsweredText + "</legend>"+
                            number_answer.parents('.ays-abs-fs').html()+
                      "</fieldset>");
                    number_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(number_answer.next().val()) == 1){
                        number_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;
                    }else{
                        number_answer.css('background-color', 'rgba(243,134,129,0.4)');
                        var rightAnswerText = '<div class="ays-text-right-answer">'+
                            number_answer.attr('chishtpatasxan')+
                            '</div>';
                        if(number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            number_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').slideDown(500);
                    }
                }
                if(short_text_answer.val() == ''){
                    var rightAnswerText = '<div class="ays-text-right-answer">';
                    var thisQuestionAnswer = short_text_answer.attr('chishtpatasxan');
                    if(typeof thisQuestionAnswer != 'undefined'){
                        thisQuestionAnswer = thisQuestionAnswer.toLowerCase().split('%%%');
                    }else{
                        thisQuestionAnswer = [''];
                    }
                    rightAnswerText += thisQuestionAnswer[0].trim();
                    
                    rightAnswerText += '</div>';
                    if(short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                        short_text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                    }
                    short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').show();
                    short_text_answer.css('background-color', 'rgba(243,134,129,0.8)');
                    short_text_answer.parents('.ays-abs-fs').html("<fieldset class='ays_fieldset'>"+
                            "<legend>" + quizLangObj.notAnsweredText + "</legend>"+
                            short_text_answer.parents('.ays-abs-fs').html()+
                      "</fieldset>");
                    short_text_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(short_text_answer.next().val()) == 1){
                        short_text_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;
                    }else{
                        short_text_answer.css('background-color', 'rgba(243,134,129,0.4)');
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = short_text_answer.attr('chishtpatasxan');
                        if(typeof thisQuestionAnswer != 'undefined'){
                            thisQuestionAnswer = thisQuestionAnswer.toLowerCase().split('%%%');
                        }else{
                            thisQuestionAnswer = [''];
                        }
                        rightAnswerText += thisQuestionAnswer[0].trim();
                        
                        rightAnswerText += '</div>';
                        if(short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            short_text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').slideDown(500);
                    }
                }
                if(date_answer.val() == ''){
                    var rightAnswerText = '<div class="ays-text-right-answer">';
                    var thisQuestionAnswer = date_answer.attr('chishtpatasxan');

                    var correctDate = new Date(thisQuestionAnswer),
                        correctDateYear = correctDate.getFullYear(),
                        correctDateMonth = (correctDate.getMonth() + 1) < 10 ? "0"+(correctDate.getMonth() + 1) : (correctDate.getMonth() + 1),
                        correctDateDay = (correctDate.getDate() < 10) ? "0"+correctDate.getDate() : correctDate.getDate();
                    rightAnswerText += [correctDateMonth, correctDateDay, correctDateYear].join('/');
                    
                    rightAnswerText += '</div>';
                    if(date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                        date_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                    }
                    date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').show();
                    date_answer.css('background-color', 'rgba(243,134,129,0.8)');
                    date_answer.parents('.ays-abs-fs').html("<fieldset class='ays_fieldset'>"+
                            "<legend>" + quizLangObj.notAnsweredText + "</legend>"+
                            date_answer.parents('.ays-abs-fs').html()+
                      "</fieldset>");
                    date_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(date_answer.next().val()) == 1){
                        date_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;
                    }else{
                        date_answer.css('background-color', 'rgba(243,134,129,0.4)');
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = date_answer.attr('chishtpatasxan');
                        var correctDate = new Date(thisQuestionAnswer),
                            correctDateYear = correctDate.getFullYear(),
                            correctDateMonth = (correctDate.getMonth() + 1) < 10 ? "0"+(correctDate.getMonth() + 1) : (correctDate.getMonth() + 1),
                            correctDateDay = (correctDate.getDate() < 10) ? "0"+correctDate.getDate() : correctDate.getDate();
                        rightAnswerText += [correctDateMonth, correctDateDay, correctDateYear].join('/');
                        
                        rightAnswerText += '</div>';
                        if(date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            date_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').slideDown(500);
                    }
                }

                checked_inputs.each(function (element, item) {
                    if (checked_inputs.length === 1) {
                        if(parseInt(checked_inputs.prev().val()) === 1){
                            checked_inputs.next().addClass('correct answered');
                            answerIsRight = true;
                        }else{
                            checked_inputs.next().addClass('wrong wrong_div answered');
                        }
                        $( checked_inputs).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', true);

                    }else if(checked_inputs.length > 1){
                        var checked_right = 0;
                        checked_inputs.map(function() {
                            if(parseInt($(this).prev().val()) === 1){
                                $(this).next().addClass('correct answered');
                            } else {
                                $(this).next().addClass('wrong wrong_div answered');
                                checked_right++;
                            }
                            $(this).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', true);
                        });
                        if(checked_right == 0){
                            answerIsRight = true;
                        }
                    }

                });
                if(myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_results_page' || myOptions.answers_rw_texts == 'on_both')){
                    if(answerIsRight){
                        question.find('.right_answer_text').css("display", "block");
                    }else{
                        question.find('.wrong_answer_text').css("display", "block");
                    }
                }else{
                    question.find('.right_answer_text').css("display", "none");
                    question.find('.wrong_answer_text').css("display", "none");
                }
                var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                if(showExplanationOn == 'on_results_page' || showExplanationOn == 'on_both'){
                    if(! question.hasClass('not_influence_to_score')){
                        question.find('.ays_questtion_explanation').css("display", "block");
                    }
                }else{
                    question.find('.ays_questtion_explanation').css("display", "none");
                }
                
                question.find('.ays_user_explanation').css("display", "none");
                question.css("pointer-events", "auto");
                question.find('.ays-quiz-answers').css("pointer-events", "none");
                formResults.append(question);
            }
        }
        form.find('.ays_quiz_results').slideDown(1000);
        form.find('.ays_quiz_rete').fadeIn(250);
        form.find('.for_quiz_rate').rating({
            onRate: function(res){
                $(this).rating('disable');
                $(this).parent().find('.for_quiz_rate_reason').slideDown(500);
                $(this).parents('.ays_quiz_rete').attr('data-rate_score', res);
            }
        });
        var aysQuizLoader = form.find('div[data-role="loader"]');
        aysQuizLoader.addClass('ays-loader');
        aysQuizLoader.removeClass(aysQuizLoader.data('class'));
        form.find('.ays_quiz_results_page').css({'display':'block'});
        form.css({'display':'block'});
        form.find('.ays_quiz_rete .for_quiz_rate_reason .action-button').on('click', function(){
            $(this).parents('.ays_quiz_rete').find('.lds-spinner-none').addClass('lds-spinner').removeClass('lds-spinner-none');
            if(myOptions.enable_quiz_rate == 'on' && myOptions.enable_rate_comments == 'on'){
                $(this).parents('.ays_quiz_rete').find('.lds-spinner2-none').addClass('lds-spinner2').removeClass('lds-spinner2-none');
            }
            var data = {};
            var quizId = form.parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
            data.action = 'ays_rate_the_quiz';
            data.rate_reason = $(this).parents('.for_quiz_rate_reason').find('.quiz_rate_reason').val();
            data.rate_score = $(this).parents('.ays_quiz_rete').data('rate_score');
            data.rate_date = GetFullDateTime();
            data.quiz_id = quizId;
            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.status === true){
                        form.find('.for_quiz_rate_reason').slideUp(800);
                        setTimeout(function(){
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').attr('data-rating', response.score);
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').rating({
                                initialRating: response.score
                            });
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').rating('disable');
                            form.find('.lds-spinner').addClass('lds-spinner-none').removeClass('lds-spinner');
                            form.find('.for_quiz_rate_reason').html('<p>'+response.rates_count + ' votes, '+response.avg_score + ' avg </p>');
                            form.find('.for_quiz_rate_reason').fadeIn(250);     
                            if(myOptions.enable_quiz_rate == 'on' && myOptions.enable_rate_comments == 'on'){
                                var data = {};
                                data.action = 'ays_get_rate_last_reviews';
                                data.quiz_id = response.quiz_id;
                                $.ajax({
                                    url: quiz_maker_ajax_public.ajax_url,
                                    method: 'post',
                                    data: data,
                                    success: function(response){
                                        form.find('.quiz_rate_reasons_body').html(response);
                                        form.find('.lds-spinner2').addClass('lds-spinner2-none').removeClass('lds-spinner2');
                                        form.find('.quiz_rate_reasons_container').slideDown(500);
                                        form.find('button.ays_load_more_review').on('click', function(e){
                                            form.find('.quiz_rate_load_more [data-role="loader"]').addClass(form.find('.quiz_rate_load_more .ays-loader').data('class')).removeClass('ays-loader');
                                            var startFrom = parseInt($(e.target).attr('startfrom'));
                                            var zuyga = parseInt($(e.target).attr('zuyga'));
                                            $.ajax({
                                                url: quiz_maker_ajax_public.ajax_url,
                                                method: 'post',
                                                data:{
                                                    action: 'ays_load_more_reviews',
                                                    quiz_id: quizId,
                                                    start_from: startFrom,
                                                    zuyga: zuyga
                                                },
                                                success: function(resp){
                                                    if(zuyga == 0){
                                                        zuyga = 1;
                                                    }else{
                                                        zuyga = 0;
                                                    }
                                                    
                                                    form.find('.quiz_rate_load_more [data-role="loader"]').addClass('ays-loader').removeClass(form.find('.quiz_rate_load_more .ays-loader').data('class'));
                                                    form.find('.quiz_rate_reasons_container').append(resp);
                                                    form.find('.quiz_rate_more_review:last-of-type').slideDown(500);
                                                    $(e.target).attr('startfrom', startFrom + 5 );
                                                    $(e.target).attr('zuyga', zuyga);
                                                    if(form.find('.quiz_rate_reasons_container p.ays_no_more').length > 0){
                                                        $(e.target).remove();
                                                    }
                                                }
                                            });
                                        });
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {}
                                });
                            }
                        },1000);
                    }
                }
            });
        });
        if (myOptions.redirect_after_submit && myOptions.redirect_after_submit == 'on') {            
            var ays_block_element = form.parents('.ays-quiz-container');
            var redirectUrl = myOptions.submit_redirect_after ? myOptions.submit_redirect_after : '';
            console.log(redirectUrl);
            if(redirectUrl == ''){
                return false;
            }
            var timer = parseInt(myOptions.submit_redirect_delay);
            if(timer === NaN){
                timer = 0;
            }
            var tabTitle = document.title;
            var timerText = $('<section class="ays_quiz_timer_container">'+
                '<div class="ays-quiz-timer">'+
                'Redirecting after ' + redirectUrl + 
                '</div><hr></section>');
            ays_block_element.prepend(timerText);
            ays_block_element.find('.ays_quiz_timer_container').css({
                height: 'auto'
            });
            setTimeout(function(){
                if (timer !== NaN) {
                    timer += 2;
                    if (timer !== undefined) {
                        var countDownDate = new Date().getTime() + (timer * 1000);
                        ays_block_element.find('div.ays-quiz-timer').slideUp(500);
                        var x = setInterval(function () {
                            var now = new Date().getTime();
                            var distance = countDownDate - Math.ceil(now/1000)*1000;
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            var timeForShow = "";
                            if(hours <= 0){
                                hours = null;
                            }else if (hours < 10) {
                                hours = '0' + hours;
                            }
                            if (minutes < 10) {
                                minutes = '0' + minutes;
                            }
                            if (seconds < 10) {
                                seconds = '0' + seconds;
                            }
                            timeForShow =  ((hours==null)? "" : (hours + ":")) + minutes + ":" + seconds;
                            if(distance <=1000){
                                timeForShow = ((hours==null) ? "" : "00:") + "00:00";
                                ays_block_element.find('div.ays-quiz-timer').html(timeForShow);
                                document.title = timeForShow + " - " + tabTitle;
                            }else{
                                ays_block_element.find('div.ays-quiz-timer').html(timeForShow);
                                document.title = timeForShow + " - " + tabTitle;
                            }
                            ays_block_element.find('div.ays-quiz-timer').slideDown(500);
                            var ays_block_element_redirect_url = myOptions.submit_redirect_url;
                            if (distance <= 1000) {
                                clearInterval(x);
                                window.location = ays_block_element_redirect_url;
                            }
                        }, 1000);
                    }
                }
            }, 2000);
        }
    }
    
})(jQuery);