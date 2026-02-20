import $ from 'jquery';
import { Modal } from 'bootstrap';
import { viewAlert } from './commonAjax';

$(document).ready(function() {
    $('input[type=text],input[type=password]').not('.arabic, .french').on('keyup', function() {
        return blockspecialchar_first(this);
    });

    $('input[type=text],textarea').not('#dqs, .arabic, .french').on('blur', function() {
        var thisVal = $(this).val().trim();
        $(this).val(thisVal);
    });

    $(document).on('input change', '.clsNumeric', function(e) {

        var val = $(this).val();

        var match = val.match(/\D/);

        if (match) {

            $(this).val('');
            viewAlert('Please enter numeric value', $(this).attr('id'));

        }
    });

    $(".disableCP").bind("cut copy paste", function(e) {
        e.preventDefault();
    });

    $('input[type=text]').attr('autocomplete', 'off');

    //FUnction to disable speacial characters on keyup :by: T Ketaki Debadarshini 
    $('input[type=text],textarea').not('.amhfld,#dqs, .arabic, .french, .odia').on('keypress', function(e) {

        var key = e.keyCode || e.which;        

        var regex = new RegExp("^[a-zA-Z0-9\-_(),.@+:/\n\\s]*$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str) || key === 9 || key === 13 || key === 8 || key === 37 || key === 38 || key === 39 || key === 40) {

            return true;
        }
        return false;

    }).keyup(function() {
        // the addition, which whill check the value after a keyup (triggered by Ctrl+V)
        // We take the same regex as for allowedChars, but we add ^ after the first bracket : it means "all character BUT these"
        var forbiddenChars = new RegExp("[^a-zA-Z0-9\-_(),.@+:'/\n\\s]", 'g');
        if (!forbiddenChars.test($(this).val()) || forbiddenChars.test($(this).val())) {
            $(this).val($(this).val().replace(forbiddenChars, ''));
        }
    });

    $('input[type=text].amhfld,textarea.amhfld').on('input', function(e) {
        var key = e.keyCode || e.which;

        var forbiddenChars = new RegExp("[';=]+");

        var str = $(this).val();

        if (!XRegExp('\\p{Ethiopic}').test(str)) {

            $(this).val('');
            return true;
        }

        re = /[a-zA-Z0-9`~!#$%^&*()@_|+\-=?;:'"<>\{\}\[\]\\\/]/gi;
        var isSplChar = re.test(str);
        if (isSplChar) {
            var no_spl_char = str.replace(/[a-zA-Z0-9`~!#$%^&*()@_|+\-=?;:'"<>\{\}\[\]\\\/]/gi, '');
            $(this).val(no_spl_char);
        }



        return false;

    }).keypress(function(e) {


        var char = String.fromCharCode(!e.charCode ? e.which : e.charCode);

        if (!XRegExp('\\p{Ethiopic}').test(char) && char != ' ') {
            e.preventDefault();
            return false;
        }

        // var key = e.keyCode || e.which;
        // console.log('char detected: '+char);
        // var regex = new RegExp("[';=]+");
        // if ( regex.test(char) ){ 

        //     console.log('sql chars detected')
        //     e.preventDefault();
        //     return false;
        // }else{
        //     return true;
        // }            

    })


    //FUnction to disable speacial characters on keyup :by: T Ketaki Debadarshini 
    $('input[type=password]').on('keypress', function(e) {
        var key = e.keyCode || e.which;

        var regex = new RegExp("^[a-zA-Z0-9\_@$#/\n ]*$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str) || key === 9 || key === 13 || key === 8 || key === 37 || key === 38 || key === 39 || key === 40) {
            return true;
        }
        return false;

    }).keyup(function() {
        // the addition, which whill check the value after a keyup (triggered by Ctrl+V)
        // We take the same regex as for allowedChars, but we add ^ after the first bracket : it means "all character BUT these"
        var forbiddenChars = new RegExp("[^a-zA-Z0-9\_@$#/\n ]", 'g');
        if (forbiddenChars.test($(this).val())) {
            $(this).val($(this).val().replace(forbiddenChars, ''));
        }
    });
});
//============ Function to check maximum length of the field ===============
export function maxLength(controlId, ctrlLen, fieldName) {

    const element = $('#' + controlId);

    if (element.length === 0) {
        console.warn(`Element #${controlId} not found`);
        return false;
    }

    const value = element.val() || '';

    if (value.length > ctrlLen) {
        viewAlert(`${fieldName} cannot be more than ${ctrlLen} characters !!!`, controlId);
        element.focus();
        return false;
    }

    return true;
}
//============ Function to check minimum length of the field ===============
export  function minLength(controlId, ctrlLen, fieldName) {
    if ($('#' + controlId).val().length < ctrlLen && $('#' + controlId).val().length > 0) {
        viewAlert(fieldName + ' cannot be less than ' + ctrlLen + ' characters !!!', controlId);
        $('#' + controlId).focus();
        return false;
    }
    return true;
}
//============ Function to check equal length of the field ===============
export  function equalLength(controlId, ctrlLen, fieldName) {
    if ($('#' + controlId).val().length != ctrlLen && $('#' + controlId).val().length > 0) {
        viewAlert(fieldName + ' should be ' + ctrlLen + ' digits length !!!', controlId);
        $('#' + controlId).focus();
        return false;
    }
    return true;
}
//============ Function to check field having no value ===============
export  function blankCheck(controlId, msg) {
    if ($('#' + controlId).val() == '') {

        viewAlert(msg, controlId);
        $('#' + controlId).focus();
        return false;
    }
    return true;
}

export  function blankCheckN(controlId, msg) {

    if ($('#' + controlId).val() == '') {

        viewAlert(msg, controlId);
        //alert($('#'+controlId).parent().parent().parent().parent().attr('id'));
        var parentId = $('#' + controlId).parent().parent().parent().parent().attr('id');
        $('#' + parentId).addClass('in');
        $('#' + parentId).attr('aria-expanded', true);
        $("#" + parentId).removeAttr("style")
        $('#' + controlId).focus();
        return false;
    }
    return true;
}
//============ Function to check radio button or checkbox validation ===============
export  function blankChkRad(controlName, msg) {
    if ($('input[name="' + controlName + '"]:checked').length <= 0) {
        viewAlert(msg);
        $('input[name="' + controlName + '"]:first').focus();
        return false;
    }
    return true;
}
//============ Function to block special characters on first place of the field on key press ===============
export  function blockspecialchar_first(e) {
    var str;
    str = e.value;
    var idName = e.id;
    // alert(str.charCodeAt(0));
    switch (str.charCodeAt(0)) {
        case 44:
            {
                //viewAlert(", Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 47:
            {
                //viewAlert("/ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 58:
            {
                //viewAlert(": Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 46:
            {
                //viewAlert(". Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 39:
            {
                //viewAlert("Single Quote not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 32:
            {
                //viewAlert("White Space not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 40:
            {
                //viewAlert("( Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 41:
            {
                //viewAlert(") Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 45:
            {
                //viewAlert("- Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 95:
            {
                //viewAlert("_ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 59:
            {
                //viewAlert("; Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 124:
            {
                //viewAlert("| Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 63:
            {
                //viewAlert("? Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

            /*case 64:
             {
             viewAlert("@ Not allowed in 1st Place!!!");
             e.value = "";
             e.focus();
             return false;
             }*/

        case 34:
            {
                //viewAlert('" Not allowed in 1st Place!!!', idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 35:
            {
                //viewAlert("# Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 36:
            {
                //viewAlert("$ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 38:
            {
                //viewAlert("& Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 126:
            {
                //viewAlert("~ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 96:
            {
                //viewAlert("` Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 33:
            {
                //viewAlert("! Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 37:
            {
                //viewAlert("% Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 94:
            {
                //viewAlert("^ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 42:
            {
                //viewAlert("* Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }
        case 92:
            {
                //viewAlert("\\ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 43:
            {
                //viewAlert("+ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }
        case 61:
            {
                //viewAlert("= Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }
        case 123:
            {
                //viewAlert("{ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 125:
            {
                //viewAlert("} Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 91:
            {
                //viewAlert("[ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 93:
            {
                //viewAlert("] Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 60:
            {
                //viewAlert("< Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }

        case 62:
            {
                //viewAlert("> Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }
        case 64:
            {
                //viewAlert("@ Not allowed in 1st Place!!!", idName);
                e.value = "";
                e.focus();
                return false;
            }
    }

}
//============ Function to block special characters in the field on key press ===============
export function blockspecialchar(event) {
    var str;
    str = event.keyCode;
    switch (str) {
        case 44:
            {
                return false;
            }

        case 47:
            {
                return false;
            }

        case 58:
            {
                return false;
            }

        case 46:
            {
                return false;
            }

        case 39:
            {
                return false;
            }

        case 32:
            {
                return false;
            }

        case 40:
            {
                return false;
            }

        case 41:
            {
                return false;
            }

        case 45:
            {
                return false;
            }

        case 95:
            {
                return false;
            }

        case 59:
            {
                return false;
            }

        case 124:
            {
                return false;
            }

        case 63:
            {
                return false;
            }

        case 64:
            {
                return false;
            }

        case 34:
            {
                return false;
            }

        case 35:
            {
                return false;
            }

        case 36:
            {
                return false;
            }

        case 38:
            {
                return false;
            }

        case 126:
            {
                return false;
            }

        case 96:
            {
                return false;
            }

        case 33:
            {
                return false;
            }

        case 37:
            {
                return false;
            }

        case 94:
            {
                return false;
            }

        case 42:
            {
                return false;
            }
        case 92:
            {
                return false;
            }

        case 43:
            {
                return false;
            }
        case 61:
            {
                return false;
            }
        case 123:
            {
                return false;
            }

        case 125:
            {
                return false;
            }

        case 91:
            {
                return false;
            }

        case 93:
            {
                return false;
            }

        case 60:
            {
                return false;
            }

        case 62:
            {
                return false;
            }
    }
}
//============ Function to block characters entry in the field on key press ===============	
export function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;

}
//============ Function to block Numeric value entry in the field on key press ===============	
export function isCharCon(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode

    if (charCode == 32 || charCode == 64 || charCode == 44 || charCode == 46)
        return false;
    return true;
}

export function isCharKey(evt) {

    var charCode = (evt.which) ? evt.which : event.keyCode
    if ((charCode > 31 && (charCode < 48 || charCode > 57)) || charCode == 13 || charCode == 8)
        return true;

    return false;
}
//============ Function to block Numeric value entry only characters on key press ===============	
export function isOnlyChar(evt) {

    var charCode2 = (evt.which) ? evt.which : event.keyCode
    if (charCode2 > 31 && (charCode2 < 65 || charCode2 > 90) &&
        (charCode2 < 97 || charCode2 > 122)) {
        //alert("Enter letters only.");
        return false;
    }
    return true;

}
//============ Function to check field value is decimal ===============
export function isDecimal(controlId) {
    var data = $('#' + controlId).val();

    if (data != ' ') {
        var reg = new RegExp(/^[0-9]+(\.[0-9]{1,2})?$/);
        if (reg.test(data) == true)
            return true;
        else {
            if (data != '') {
                viewAlert("Enter only decimal values having 2 digit after decimal", controlId);
                $('#' + controlId).focus();
                return false;
            } else {
                return true;
            }
        }
    }
}
//============ Function to check field value is decimal with 2 digit after decimal and maxLength ===============
export function checkMaxDecimal(controlId, maxlen) {
    var reg = new RegExp(/^\d+(\.\d{1,2})?$/);
    var eVal = $('#' + controlId).val();
    var flen = eVal.indexOf(".");
    var spl = eVal.split(".");
    if (flen == -1) {
        if (eVal.length > maxlen) {
            viewAlert("Enter decimal value of total " + maxlen + " character with 2 digit after decimal", controlId);
            $('#' + controlId).focus();
            return false;
        } else
            return true;
    } else if (spl[0].length <= 10 && reg.test(eVal) == true) {
        return true;
    } else {
        viewAlert("Enter decimal value of total " + maxlen + " character with 2 digit after decimal", controlId);
        $('#' + controlId).focus();
        return false;
    }
}

//Function to textcounter.
export function TextCounter(ctlTxtName, lblCouter, numTextSize) { 
    var txtName = $('#' + ctlTxtName).val();
    //console.log(txtName)
    var txtNameLength = txtName.length;
    if (parseInt(txtNameLength) > parseInt(numTextSize)) {
        var txtMaxTextSize = txtName.substr(0, numTextSize);
        $("#" + ctlTxtName).val(txtMaxTextSize);
        //alert("Entered Text Exceeds '" + numTextSize + "' Characters.");
        viewAlert("Entered Text Exceeds '" + numTextSize + "' Characters.", ctlTxtName);
        $('#' + ctlTxtName).focus();
        $("#" + lblCouter).text(0);
        return false;
    } else {
        $("#" + lblCouter).text(parseInt(numTextSize) - parseInt(txtNameLength));
        return true;
    }
}


//============ Function to check field value is decimal with variable no. of digit before decimal and variable no. of digit after decimal and maxLength ===============
export function checkMaxDecimalWithLimit(controlId, maxlen, leftValue, rightValue) {
    var testPattern = "^\\d{1," + leftValue + "}\\.\\d{1," + rightValue + "}?$";

    var reg = new RegExp(testPattern, "i");
    var eVal = $('#' + controlId).val();
    var flen = eVal.indexOf(".");
    var spl = eVal.split(".");

    if (flen == -1) {
        if (Number(eVal.length) > Number(leftValue)) {
            //viewAlert("Enter decimal value of "+leftValue+" digit before decimal and "+rightValue+" digit after decimal", controlId);
            viewAlert("Enter decimal value of maximum " + leftValue + " digit", controlId);
            $('#' + controlId).focus();
            return false;
        } else
            return true;
    } else if (spl[0].length <= leftValue && spl[1].length <= rightValue && reg.test(eVal) == true) {
        return true;
    } else {

        viewAlert("Enter decimal value of " + leftValue + " digit before decimal and " + rightValue + " digit after decimal", controlId);
        $('#' + controlId).focus();
        return false;
    }
}
//============ Function to check field value is only numeric ===============
export function validateNumber(controlId) {
    var numPattern = new RegExp(/^\d+$/);
    var txtVal = $('#' + controlId).val();
    if (txtVal != '') {
        if (numPattern.test(txtVal) == true)
            return true;
        else {
            viewAlert("Enter only numeric values", controlId);
            $('#' + controlId).focus();
            return false;
        }
    } else
        return true;
}

export function validateCharOnly(controlId) {
    var charPattern = new RegExp(/^[a-zA-Z]+$/);
    var txtVal = $('#' + controlId).val();
    if (txtVal != '') {
        if (charPattern.test(txtVal) == true)
            return true;
        else {
            viewAlert("Enter only characters", controlId);
            $('#' + controlId).focus();
            return false;
        }
    } else
        return true;
}
//========== Function to validate char and number only By Sunil Kumar Parida  on 11-Mar-2015 ===============
export function validateCharNumber(controlId, fieldName) {
    var txtVal = $('#' + controlId).val();
    if (txtVal != '') {
        if (/^[a-zA-Z0-9]+$/.test(txtVal)) {
            return true;
        } else {
            viewAlert(fieldName + ' accept only number and characters', controlId);
            $('#' + controlId).focus();
            return false;
        }
    } else
        return true;
}
//============ Function to check space in last position ===============
export function whiteSpaceLast(controlId) {
    var myString = $('#' + controlId).val();
    var lastChar = myString[myString.length - 1];
    if (lastChar == ' ') {
        viewAlert('Please remove space from last', controlId);
        $('#' + controlId).focus();
        return false;
    }
    return true;
}
//============ Function to check special character  ===============
export function checkSpecialChar(controlId) {
    var splArr = ["'", "%", "=", "<", ">", "\\", "\"", "--"];
    //var splArr = ["%", "<", ">", "\\", "\""];
    var str = $('#' + controlId).val();
    for (var i = 0; i < splArr.length; i++) {
        if (str.indexOf(splArr[i]) > 0) {
            viewAlert("Special character " + splArr[i] + " is not allowed !!!", controlId);
            $('#' + controlId).focus();
            return false;
        }
    }
    return true;
}
//============ Function to check special character  ===============
export function checkSpecialChar2(controlId) {
    var splArr = ["=", "<", ">", "\\", "\"", "--"];
    //var splArr = ["%", "<", ">", "\\", "\""];
    var str = $('#' + controlId).val();
    for (var i = 0; i < splArr.length; i++) {
        if (str.indexOf(splArr[i]) > 0) {
            viewAlert("Special character " + splArr[i] + " is not allowed !!!", controlId);
            $('#' + controlId).focus();
            return false;
        }
    }
    return true;
}
//============ Function to check dropdown is selected  ===============
export function selectDropdown(controlId, msg) {
    var ddlVal = $('#' + controlId).val();
    if (ddlVal == '0' || ddlVal == '' || ddlVal == null) {
        viewAlert(msg, controlId);
        $('#' + controlId).focus();
        return false;
    }
    return true;
}
//============ Function to allow valid decimal value on ley press ===============
export function validDesimal(evt, obj) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var txtVal = $('#' + obj).val();
    var txtValLen = txtVal.length;
    if ((charCode > 47 && charCode < 58) || charCode == 46 || charCode == 8) {
        if (txtVal.indexOf(".") > 0 && charCode == 46)
            return false;
        else
            return true;
    }
    return false;
}

//============ Function to allow valid decimal value on ley press with Class ===============
export function validDesimalCls(evt, obj) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var txtVal = $('.' + obj).val();
    var txtValLen = txtVal.length;
    if ((charCode > 47 && charCode < 58) || charCode == 46 || charCode == 8) {
        if (txtVal.indexOf(".") > 0 && charCode == 46)
            return false;
        else
            return true;
    }
    return false;
}

//============ Function to allow valid decimal value on ley press ===============
export function isValidDesimal(evt, obj) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var txtVal = $(obj).val();
    var txtValLen = txtVal.length;
    if ((charCode > 47 && charCode < 58) || charCode == 46 || charCode == 8) {
        if (txtVal.indexOf(".") > 0 && charCode == 46)
            return false;
        else
            return true;
    }
    return false;
}
//============ Function to check upload file types ===============
export function IsCheckFile(ControlName, msg, strFileType) {

    var arrFileType = strFileType.split(',');
    var filename = $('#' + ControlName).val();
    var fileLength = filename.length;
    if (fileLength == 0)
        return true;
    else {
        var extnIndex = filename.lastIndexOf(".") + 1;
        var fileType = filename.substring(extnIndex, fileLength).toLowerCase();

        for (var i = 0; i < arrFileType.length; i++) {
            if (fileType == arrFileType[i])
                return true;
        }
        viewAlert(msg + ' Upload (<span class="fileTypeS">.' + strFileType + '</span>) file only', ControlName);
        $('#' + ControlName).focus();
        return false;
    }
}
//============ Function to validate email ===============
export function validEmail(controlId) {
    var pattern = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
    var email = $('#' + controlId).val();
    if (email != '') {
        if (pattern.test(email) == true)
            return true;
        else {
            viewAlert("Please enter a valid email id", controlId);
            $('#' + controlId).focus();
            return false;
        }
    } else
        return true;
}

//=============== Function to compare date ==============
export function compareDate(firstControl, secondControl, field1Name, field2Name, msg) {

    var firstDate = $('#' + firstControl).val();
    var secondDate = $('#' + secondControl).val();
    var splitFirstDt = firstDate.split("-");
    firstDate = splitFirstDt[2] + '-' + splitFirstDt[1] + '-' + splitFirstDt[0];
    var splitSecondDt = secondDate.split("-");
    secondDate = splitSecondDt[2] + '-' + splitSecondDt[1] + '-' + splitSecondDt[0];
    firstDate = new Date(firstDate);
    secondDate = new Date(secondDate);

    if (firstDate >= secondDate) {
        if (typeof msg == "undefined")
            viewAlert(field1Name + " cannot be greater than or equal to " + field2Name, firstControl);
        else
            viewAlert(msg, firstControl);
        $('#' + secondControl).focus();
        return false;
    } else
        return true;
}

export function compareDate2(firstControl, secondControl, field1Name, field2Name, msg) {
    var firstDate = $('#' + firstControl).val();
    var secondDate = $('#' + secondControl).val();
    var splitFirstDt = firstDate.split("-");
    firstDate = splitFirstDt[2] + '-' + splitFirstDt[1] + '-' + splitFirstDt[0];
    var splitSecondDt = secondDate.split("-");
    secondDate = splitSecondDt[2] + '-' + splitSecondDt[1] + '-' + splitSecondDt[0];
    firstDate = new Date(firstDate);
    secondDate = new Date(secondDate);

    if (firstDate > secondDate) {
        if (typeof msg == "undefined")
            viewAlert(field1Name + " cannot be greater than to " + field2Name, firstControl);
        else
            viewAlert(msg, firstControl);
        $('#' + secondControl).focus();
        return false;
    } else
        return true;
}

//=============== Function to compare date with current date ==============
export function compareCurDate(controlId, fieldName, flag) {
    //================ set flag as 'g' for not greater than current date and 'l' for not less than current date ========
    var fieldDate = $('#' + controlId).val();
    var splFieldDt = fieldDate.split("-");
    fieldDate = splFieldDt[2] + '-' + splFieldDt[1] + '-' + splFieldDt[0];
    var curDate = new Date((new Date()).setHours(0, 0, 0, 0));
    fieldDate = new Date((new Date(fieldDate)).setHours(0, 0, 0, 0));
    if (flag.toLowerCase() == 'g') {
        if (fieldDate > curDate) {
            viewAlert(fieldName + " cannot be greater than current date", controlId);
            $('#' + controlId).focus();
            return false;
        } else
            return true;
    } else {
        if (curDate > fieldDate) {
            viewAlert(fieldName + " cannot be less than current date", controlId);
            $('#' + controlId).focus();
            return false;
        } else
            return true;
    }
}
//=============== Function to for alias ==============
export function checkAlias(controlId) {

    var $input = $('#' + controlId);
    var value = $input.val();

    // 1️⃣ Convert spaces to hyphen
    value = value.replace(/\s+/g, '-');

    // 2️⃣ Convert to lowercase (optional but recommended)
    value = value.toLowerCase();

    // 3️⃣ Remove all characters except a-z, 0-9 and hyphen
    value = value.replace(/[^a-z0-9-]/g, '-');

    // 4️⃣ Prevent multiple consecutive hyphens
    value = value.replace(/-+/g, '-');

    // 5️⃣ Prevent hyphen at start or end
    value = value.replace(/^-|-$/g, '');

    // Set cleaned value back to input
    $input.val(value);

    // Final validation check
    var regex = /^[a-z0-9]+(-[a-z0-9]+)*$/;

    if (!regex.test(value)) {
        // viewAlert("Only letters, numbers and single hyphen between words are allowed!", controlId);
        $input.focus();
        return false;
    }

    return true;
}

//============ Function to Number and slash By Rasmi Ranjan swain on 15-oct-2014 ===============
function validateNumberSlash(controlId, msg) {
    var numPattern = new RegExp(/^\d\/\d$/);
    var txtVal = $('#' + controlId).val();
    if (txtVal != '') {
        if (numPattern.test(txtVal) == true)
            return true;
        else {

            $('#' + controlId).focus();
            viewAlert(msg, controlId);
            return false;
        }
    } else
        return true;

}

//============ Function to check Valid URL By Rasmi Ranjan swain on 06-Jan-2015 ===============
function validURL(controlId, msg) {
    var numPattern = new RegExp(/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/);
    var txtVal = $('#' + controlId).val();
    if (txtVal != '') {
        if (numPattern.test(txtVal) == true)
            return true;
        else {

            $('#' + controlId).focus();
            viewAlert(msg, controlId);
            return false;
        }
    } else
        return true;

}

function validURLWHTTP(controlId, msg) {
    //    var numPattern = new RegExp(/(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/);
    var numPattern = new RegExp(/^((ftp|http|https)?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/);
    var txtVal = $('#' + controlId).val();
    if (txtVal != '') {
        if (numPattern.test(txtVal) == true)
            return true;
        else {

            $('#' + controlId).focus();
            viewAlert(msg, controlId);
            return false;
        }
    } else
        return true;

}

function isAlphaNumeric(controlId, msg) {
    var numPattern = new RegExp(/^([0-9]|[a-z])+([a-z]+)$/i);
    var txtVal = $('#' + controlId).val();
    if (txtVal != '') {
        if (numPattern.test(txtVal) == true)
            return true;
        else {

            $('#' + controlId).focus();
            viewAlert(msg, controlId);
            return false;
        }
    } else
        return true;
}
//============ Function to check Valid Phone No By Rasmi Ranjan swain on 28-Jan-2015 ===============
function validatePhone(controlId, msg) {
    var txtVal = $('#' + controlId).val();
    var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    //var filter = /^[0-9]{4}[ \-][0-9]{7}?$/;
    if (txtVal != '') {
        if (filter.test(txtVal))
            return true;
        else {
            $('#' + controlId).focus();
            viewAlert(msg);
            return false;
        }
    } else
        return true;
}


//============ Function to check Valid Phone No By Rasmi Ranjan swain on 28-Jan-2015 ===============
function validateTel(txtVal, msg) {
    // var txtVal = $('#'+controlId).val();
    var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    //var filter = /^[0-9]{4}[ \-][0-9]{7}?$/;
    if (txtVal != '') {
        if (filter.test(txtVal))
            return true;
        else {

            viewAlert(msg);
            return false;
        }
    } else
        return true;
}



//============ Function to validate valid name No By Rasmi Ranjan swain on 28-Jan-2015 ===============
function validateName(controlId, msg) {
    var txtVal = $('#' + controlId).val();
    var filter = /^[A-Za-z]{1}/;
    if (txtVal != '') {
        if (filter.test(txtVal))
            return true;
        else {
            $('#' + controlId).focus();
            viewAlert(msg, controlId);
            return false;
        }
    } else
        return true;
}
//============ Function to validate valid name No By Rasmi Ranjan swain on 28-Jan-2015 ===============
function validMobileNo(controlId, msg) {
    var txtVal = $('#' + controlId).val();
    var filter = /^[6-9][0-9]{9}$/;
    if (txtVal != '') {
        if (filter.test(txtVal))
            return true;
        else {
            $('#' + controlId).focus();
            viewAlert(msg, controlId);
            return false;
        }
    } else
        return true;
}
//============ Function to validate valid name No By Rasmi Ranjan swain on 28-Jan-2015 ===============
function validPHno(evt, obj) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var txtVal = $('#' + obj).val();
    var txtValLen = txtVal.length;
    if ((charCode > 47 && charCode < 58) || charCode == 45 || charCode == 8) {
        if (txtVal.indexOf("-") > 0 && charCode == 45)
            return false;
        else
            return true;
    }
    return false;
}
//============ Function to validate valid name No By Rasmi Ranjan swain on 28-Jan-2015 ===============
function validUserName(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode > 47 && charCode <= 57) || (charCode >= 64 && charCode <= 122) || charCode == 36 || charCode == 8) {
        return true;
    }
    return false;
}

//============ Function to check characters and number both for Letter No By:Md. Shahnawaz Atique on Dt:08-May-2020  ===============
function checkLetterNo(controlId) {
    // Patterns = /^[a-zA-Z\d._\/\- ]+$/
    var Patterns = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9_\-\/]+)$/;
    var str = $('#' + controlId).val();
    var objregExp = new RegExp(Patterns);
    if (!(objregExp.test(str))) {
        viewAlert("Invalid letter no. format !!!", controlId);
        $('#' + controlId).focus();
        return false;
    }
    return true;
}


// if (typeof escapeHtmlEntities == 'undefined') {
//     escapeHtmlEntities = function(text) {
//         return text.replace(/[\u00A0-\u2666<>\&]/g, function(c) {
//             return '&' +
//                 (escapeHtmlEntities.entityTable[c.charCodeAt(0)] || '#' + c.charCodeAt(0)) + ';';
//         });
//     };

//     // all HTML4 entities as defined here: http://www.w3.org/TR/html4/sgml/entities.html
//     // added: amp, lt, gt, quot and apos
//     escapeHtmlEntities.entityTable = {
//         34: 'quot',
//         38: 'amp',
//         39: 'apos',
//         60: 'lt',
//         62: 'gt',
//         160: 'nbsp',
//         161: 'iexcl',
//         162: 'cent',
//         163: 'pound',
//         164: 'curren',
//         165: 'yen',
//         166: 'brvbar',
//         167: 'sect',
//         168: 'uml',
//         169: 'copy',
//         170: 'ordf',
//         171: 'laquo',
//         172: 'not',
//         173: 'shy',
//         174: 'reg',
//         175: 'macr',
//         176: 'deg',
//         177: 'plusmn',
//         178: 'sup2',
//         179: 'sup3',
//         180: 'acute',
//         181: 'micro',
//         182: 'para',
//         183: 'middot',
//         184: 'cedil',
//         185: 'sup1',
//         186: 'ordm',
//         187: 'raquo',
//         188: 'frac14',
//         189: 'frac12',
//         190: 'frac34',
//         191: 'iquest',
//         192: 'Agrave',
//         193: 'Aacute',
//         194: 'Acirc',
//         195: 'Atilde',
//         196: 'Auml',
//         197: 'Aring',
//         198: 'AElig',
//         199: 'Ccedil',
//         200: 'Egrave',
//         201: 'Eacute',
//         202: 'Ecirc',
//         203: 'Euml',
//         204: 'Igrave',
//         205: 'Iacute',
//         206: 'Icirc',
//         207: 'Iuml',
//         208: 'ETH',
//         209: 'Ntilde',
//         210: 'Ograve',
//         211: 'Oacute',
//         212: 'Ocirc',
//         213: 'Otilde',
//         214: 'Ouml',
//         215: 'times',
//         216: 'Oslash',
//         217: 'Ugrave',
//         218: 'Uacute',
//         219: 'Ucirc',
//         220: 'Uuml',
//         221: 'Yacute',
//         222: 'THORN',
//         223: 'szlig',
//         224: 'agrave',
//         225: 'aacute',
//         226: 'acirc',
//         227: 'atilde',
//         228: 'auml',
//         229: 'aring',
//         230: 'aelig',
//         231: 'ccedil',
//         232: 'egrave',
//         233: 'eacute',
//         234: 'ecirc',
//         235: 'euml',
//         236: 'igrave',
//         237: 'iacute',
//         238: 'icirc',
//         239: 'iuml',
//         240: 'eth',
//         241: 'ntilde',
//         242: 'ograve',
//         243: 'oacute',
//         244: 'ocirc',
//         245: 'otilde',
//         246: 'ouml',
//         247: 'divide',
//         248: 'oslash',
//         249: 'ugrave',
//         250: 'uacute',
//         251: 'ucirc',
//         252: 'uuml',
//         253: 'yacute',
//         254: 'thorn',
//         255: 'yuml',
//         402: 'fnof',
//         913: 'Alpha',
//         914: 'Beta',
//         915: 'Gamma',
//         916: 'Delta',
//         917: 'Epsilon',
//         918: 'Zeta',
//         919: 'Eta',
//         920: 'Theta',
//         921: 'Iota',
//         922: 'Kappa',
//         923: 'Lambda',
//         924: 'Mu',
//         925: 'Nu',
//         926: 'Xi',
//         927: 'Omicron',
//         928: 'Pi',
//         929: 'Rho',
//         931: 'Sigma',
//         932: 'Tau',
//         933: 'Upsilon',
//         934: 'Phi',
//         935: 'Chi',
//         936: 'Psi',
//         937: 'Omega',
//         945: 'alpha',
//         946: 'beta',
//         947: 'gamma',
//         948: 'delta',
//         949: 'epsilon',
//         950: 'zeta',
//         951: 'eta',
//         952: 'theta',
//         953: 'iota',
//         954: 'kappa',
//         955: 'lambda',
//         956: 'mu',
//         957: 'nu',
//         958: 'xi',
//         959: 'omicron',
//         960: 'pi',
//         961: 'rho',
//         962: 'sigmaf',
//         963: 'sigma',
//         964: 'tau',
//         965: 'upsilon',
//         966: 'phi',
//         967: 'chi',
//         968: 'psi',
//         969: 'omega',
//         977: 'thetasym',
//         978: 'upsih',
//         982: 'piv',
//         8226: 'bull',
//         8230: 'hellip',
//         8242: 'prime',
//         8243: 'Prime',
//         8254: 'oline',
//         8260: 'frasl',
//         8472: 'weierp',
//         8465: 'image',
//         8476: 'real',
//         8482: 'trade',
//         8501: 'alefsym',
//         8592: 'larr',
//         8593: 'uarr',
//         8594: 'rarr',
//         8595: 'darr',
//         8596: 'harr',
//         8629: 'crarr',
//         8656: 'lArr',
//         8657: 'uArr',
//         8658: 'rArr',
//         8659: 'dArr',
//         8660: 'hArr',
//         8704: 'forall',
//         8706: 'part',
//         8707: 'exist',
//         8709: 'empty',
//         8711: 'nabla',
//         8712: 'isin',
//         8713: 'notin',
//         8715: 'ni',
//         8719: 'prod',
//         8721: 'sum',
//         8722: 'minus',
//         8727: 'lowast',
//         8730: 'radic',
//         8733: 'prop',
//         8734: 'infin',
//         8736: 'ang',
//         8743: 'and',
//         8744: 'or',
//         8745: 'cap',
//         8746: 'cup',
//         8747: 'int',
//         8756: 'there4',
//         8764: 'sim',
//         8773: 'cong',
//         8776: 'asymp',
//         8800: 'ne',
//         8801: 'equiv',
//         8804: 'le',
//         8805: 'ge',
//         8834: 'sub',
//         8835: 'sup',
//         8836: 'nsub',
//         8838: 'sube',
//         8839: 'supe',
//         8853: 'oplus',
//         8855: 'otimes',
//         8869: 'perp',
//         8901: 'sdot',
//         8968: 'lceil',
//         8969: 'rceil',
//         8970: 'lfloor',
//         8971: 'rfloor',
//         9001: 'lang',
//         9002: 'rang',
//         9674: 'loz',
//         9824: 'spades',
//         9827: 'clubs',
//         9829: 'hearts',
//         9830: 'diams',
//         338: 'OElig',
//         339: 'oelig',
//         352: 'Scaron',
//         353: 'scaron',
//         376: 'Yuml',
//         710: 'circ',
//         732: 'tilde',
//         8194: 'ensp',
//         8195: 'emsp',
//         8201: 'thinsp',
//         8204: 'zwnj',
//         8205: 'zwj',
//         8206: 'lrm',
//         8207: 'rlm',
//         8211: 'ndash',
//         8212: 'mdash',
//         8216: 'lsquo',
//         8217: 'rsquo',
//         8218: 'sbquo',
//         8220: 'ldquo',
//         8221: 'rdquo',
//         8222: 'bdquo',
//         8224: 'dagger',
//         8225: 'Dagger',
//         8240: 'permil',
//         8249: 'lsaquo',
//         8250: 'rsaquo',
//         8364: 'euro'
//     };
// }

function chkFileSize(id, sizeInKb, sizeInMb, type) {
    var message = '';
    if (type == 1) {
        message = 'File size exceeds ' + sizeInKb + 'KB.';
    } else {
        message = 'File size exceeds ' + sizeInMb + 'MB.';
    }
    var fileSize_inKB = Math.round(($("#" + id)[0].files[0].size / 1024));
    if (fileSize_inKB > sizeInKb) {
        viewAlert(message, id);
        //scrollToPosition(id);
        return false;
    } else {
        return true;
    }
}
//============ Function to check less date time  By Rasmi Ranjan swain on 06-Jan-2015 ===============	
function lessDateTime(dateFrom, dateTo, msg) {
    var splDateFrom = dateFrom.split("-");
    var date_from = new Date(splDateFrom[2], Number(splDateFrom[1] - 1), splDateFrom[0], splDateFrom[3], splDateFrom[4]);
    var splDateTo = dateTo.split("-");
    var date_To = new Date(splDateTo[2], Number(splDateTo[1] - 1), splDateTo[0], splDateTo[3], splDateTo[4]);
    if (date_from.getTime() > date_To.getTime()) {
        viewAlert(msg, dateTo);
        return false;
    }
    return true;
}
//============ Function to check less date time  By Rasmi Ranjan swain on 06-Jan-2015 ===============	
function convert12to24(timeStr) {
    var meridian = timeStr.substr(timeStr.length - 2).toLowerCase();;
    var hours = timeStr.substr(0, timeStr.indexOf(':'));
    var minutes = timeStr.substring(timeStr.indexOf(':') + 1, timeStr.indexOf(':') + 3);
    if (meridian == 'pm') {
        if (hours != 12) {
            hours = hours * 1 + 12;
        } else {
            hours = '12';
        }
    } else {
        if (hours == 12) {
            hours = '00';
        }
    }
    return hours + '-' + minutes;
}

function changeTimeFormat(id) {
    var input = document.getElementById(id).value;
    var output = convert12to24(input);
    return output;
}

function validPassword(controlId, txtLbl) {
    var pattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,15}$/;

    var password = $('#' + controlId).val();
    if (password != '') {
        if (pattern.test(password) == true)
            return true;
        else {
            viewAlert(txtLbl + " should be alphanumeric and consist of at least one upper case letter, special character and should be 8-15 characters long", controlId);
            $('#' + controlId).focus();
            return false;
        }
    } else
        return true;
}

/*created by Bijaylaxmi Mohanty*/
function cPassMatch(newPwdId, cPwdId, msg) {
    var newPwd = $('#' + newPwdId).val();
    var cPwd = $('#' + cPwdId).val();

    if (newPwd != "" && cPwd != "") {
        if (newPwd == cPwd) {
            return true;
        } else {
            viewAlert(msg, cPwdId);
            $('#' + cPwdId).focus();
            return false;
        }
    }
}

function newPassDiff(newPwdId, oldPwdId, msg) {
    var newPwd = $('#' + newPwdId).val();
    var oldPwd = $('#' + oldPwdId).val();

    if (newPwd != "" && oldPwd != "") {
        if (newPwd == oldPwd) {
            viewAlert(msg, newPwdId);
            $('#' + newPwdId).focus();
            return false;
        } else {
            return true;
        }
    }
}

function validateEmail(controlId, msg) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    var email = $('#' + controlId).val();
    if (email != '') {
        if (emailReg.test(email) == true) {
            return true;
        }
        else {
            viewAlert(msg, controlId);
            $('#' + controlId).focus();
            return false;
        }
    }
}
//

function validIFSC(controlId, txtLbl) {
    var pattern = /^[A-Za-z]{4}\d{8}$/;

    var ifsc = $('#' + controlId).val();
    if (ifsc != '') {
        if (pattern.test(ifsc) == true)
            return true;
        else {
            viewAlert(txtLbl + " should contain 4 characters and 8 letters", controlId);
            $('#' + controlId).focus();
            return false;
        }
    } else
        return true;
}

function validPhone(controlId, msg) {
    var phoneRegExp = /^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/;
    var phoneVal = $("#" + controlId).val();
    var numbers = phoneVal.split("").length;

    if (numbers >= 10 && numbers <= 20 && phoneRegExp.test(phoneVal)) {
        return true;
    } else {
        viewAlert(msg, controlId);
        $('#' + controlId).focus();
        return false;
    }
}
// function to check duplicate ## By## Rasmi Ranjan Swain ## On ## 19-Oct-2016
function hasDuplicates(array) {
    var valuesSoFar = [];
    var flag = 0;
    for (var i = 0; i < array.length; ++i) {
        var value = array[i];
        if (valuesSoFar.indexOf(value) !== -1) {
            flag++;
        }
        valuesSoFar.push(value);
    }

    return flag;
}

function countChar(controlId, counterId) {
    var length = $("#" + controlId).val().length;
    var maxLength = $("#" + controlId).attr('maxlength');
    length = (maxLength - length);
    $("#" + counterId).find(".ct").html(length);
}
//============ Function to check Valid URL By Rasmi Ranjan swain on 06-Jan-2015 ===============
function isConNumOnly(controlId, msg, evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    else {
        if (charCode == 8) {
            return true
        } else {
            var numPattern = new RegExp(/(\d)\1{3,}/);
            var txtVal = $('#' + controlId).val();
            if (txtVal != '') {
                if (numPattern.test(txtVal) == true) {
                    $('#' + controlId).focus();
                    viewAlert(msg, controlId);
                    return false;
                } else {

                    return true;
                }
            } else
                return true;
        }

    }
    return true;
}

function isConChar(controlId, msg, evt) {

    var charCode = (evt.which) ? evt.which : evt.keyCode;

    if (charCode == 8) {
        return true;
    } else {
        var numPattern = new RegExp(/(\d)\1{3,}/);
        var txtVal = $('#' + controlId).val();
        if (txtVal != '') {
            if (numPattern.test(txtVal) == true) {
                $('#' + controlId).focus();
                viewAlert(msg, controlId);
                return false;
            } else {

                return true;
            }
        } else
            return true;
    }

}


function removeSpace(str, ctrrlId, evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 32) {
        return false;
    }
    var newStr = str.split(" ").join("");
    $("#" + ctrrlId).val(newStr);
}
//============ Function to check Valid URL By Abhiram Samantara on 01-May-2017 ===============

function convertDateFormat(inputFormat) {
    var monthNames = ["Jan", "Febr", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    function pad(s) { return (s < 10) ? '0' + s : s; }
    var d = new Date(inputFormat);
    return [pad(d.getDate()), pad(monthNames[d.getMonth()]), d.getFullYear()].join('-');
}

// function to block the 0 at first place By: Ashok kumar Samal :: On:02-04-2018
function blockZero_firstSGP(e) {
    var str;
    str = e.value;
    var idName = e.id;
    switch (str.charCodeAt(0)) {
        case 48:
            {
                viewAlert("0 not allolwed at first place", idName);
                e.value = "";
                return false;
            }
    }
}



//=============== Function to compare date ==============
function compareTwoDate(firstControl, secondControl, field1Name, field2Name) {

    var firstDate = $('#' + firstControl).val();
    var secondDate = $('#' + secondControl).val();


    var splitFirstDt = firstDate.split("-");
    firstDate = splitFirstDt[0] + '-' + splitFirstDt[1] + '-' + splitFirstDt[2];


    var splitSecondDt = secondDate.split("-");
    secondDate = splitSecondDt[0] + '-' + splitSecondDt[1] + '-' + splitSecondDt[2];


    firstDate = new Date(firstDate);
    secondDate = new Date(secondDate);


    if (firstDate > secondDate) {

        viewAlert(field1Name + " cannot be greater than " + field2Name, firstControl);

        $('#' + secondControl).focus();

        return false;
    } else
        return true;
}

//============ Function to check Custome special character  ===============
function checkSpecialCharCustome(controlId) {
    var splArr = ["%", "=", "<", ">", "\\", "\"", "--", "$", "^", "/*", "*/"];
    //var splArr = ["%", "<", ">", "\\", "\""];
    var str = $('#' + controlId).val();
    for (var i = 0; i < splArr.length; i++) {
        if (str.indexOf(splArr[i]) > 0) {
            viewAlert("Special character " + splArr[i] + " is not allowed !!!", controlId);
            $('#' + controlId).focus();
            return false;
        }
    }
    return true;
}

function isOnlyCharDot(evt) {
    var regex = new RegExp("^[a-zA-Z. ]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    }
    return true;

}

function isOnlyCharNumeric(evt) {
    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    }
    return true;

}