define(['jquery'], function($) {
    'use strict';

    function splitSubStringByLength(string, length) {
        var result = [];
        var stringLength = string.length;
        var index = 0;

        while (index < stringLength - length + 1) {
            result.push(string.substr(index, length));
            index++;
        }

        return result;
    }

    return function() {
        $.validator.addMethod(
            'validate-admin-password-length',
            function (pass) {
                if (pass == null) {
                    return false;
                }
                pass = $.trim(pass);
                if (pass.length === 0) {
                    return true;
                }

                return pass.length >= 8;
            },
            $.mage.__('Your password must be at least 8 characters.')
        );
        $.validator.addMethod(
            'validate-admin-password-character',
            function (pass) {
                if (pass == null) {
                    return false;
                }
                pass = $.trim(pass);
                if (pass.length === 0) {
                    return true;
                }

                var validConditionCount = 0;

                if (/[a-z]/u.test(pass)) {
                    validConditionCount++;
                }

                if (/[A-Z]/u.test(pass)) {
                    validConditionCount++;
                }

                if (/\d/u.test(pass)) {
                    validConditionCount++;
                }

                if (/\W/iu.test(pass)) {
                    validConditionCount++;
                }

                return validConditionCount >= 3;
            },
            $.mage.__('Passwords must contain characters from three of the following four categories: ' +
                'uppercase characters, lowercase characters, numeric characters, non-alphabetic characters: ' +
                '! @ # & ( ) – [ { } ] : ; \', ? / * ` ~ $ ^ + = < > “'
            )
        );
        $.validator.addMethod(
            'validate-admin-password-sensitive',
            function (pass) {
                if (pass == null) {
                    return false;
                }
                pass = $.trim(pass);
                if (pass.length === 0) {
                    return true;
                }

                pass = pass.toLowerCase();

                var username = $('#user_username').val() !== undefined ? $('#user_username').val() : $('#username').val() ;
                var firstName = $('#user_firstname').val() !== undefined ? $('#user_firstname').val() : $('#firstname').val() ;
                var lastName = $('#user_lastname').val() !== undefined ? $('#user_lastname').val() : $('#lastname').val() ;

                var tokens = []
                    .concat(username.toLowerCase())
                    .concat(splitSubStringByLength(firstName.toLowerCase(), 3))
                    .concat(splitSubStringByLength(lastName.toLowerCase(), 3));

                return tokens.every(function(token) {
                    return pass.indexOf(token) === -1;
                });
            },
            $.mage.__('Passwords cannot contain ' +
                'the user\'s account name or parts of the user\'s full name that exceed two consecutive characters.'
            )
        );
        $.validator.addMethod(
            'validate-cpassword',
            function () {
                var conf = $('#confirmation').length > 0 ? $('#confirmation') : $($('.validate-cpassword')[0]),
                    pass = false,
                    passwordElements, i, passwordElement;

                if ($('#password')) {
                    pass = $('#password');
                }
                passwordElements = $('.validate-password');

                for (i = 0; i < passwordElements.length; i++) {
                    passwordElement = $(passwordElements[i]);

                    if (passwordElement.closest('form').attr('id') === conf.closest('form').attr('id')) {
                        pass = passwordElement;
                    }
                }

                if ($('.validate-admin-password').length) {
                    pass = $($('.validate-admin-password')[0]);
                }

                if ($('#user_password').length) {
                    pass = $('#user_password');
                }

                return pass.val() === conf.val();
            },
            $.mage.__('Please make sure your passwords match.')
        );
    }
});
