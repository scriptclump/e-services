//Formatting for igGrid cells to display igCombo text as opposed to igCombo value
var state_wise_tax_classes = $('#vat_state_wise_tax_classes').data('vat_state_wise_tax_classes');

//Formatting for igGrid cells to display igCombo text as opposed to igCombo value
function allFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[4035].length; i++) {
        tax_value = state_wise_tax_classes[4035][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function anFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1475].length; i++) {
        tax_value = state_wise_tax_classes[1475][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function apFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1476].length; i++) {
        tax_value = state_wise_tax_classes[1476][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function arFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1477].length; i++) {
        tax_value = state_wise_tax_classes[1477][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function asFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1478].length; i++) {
        tax_value = state_wise_tax_classes[1478][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function biFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1479].length; i++) {
        tax_value = state_wise_tax_classes[1479][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function chFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1480].length; i++) {
        tax_value = state_wise_tax_classes[1480][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function daFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1481].length; i++) {
        tax_value = state_wise_tax_classes[1481][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function dmFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1482].length; i++) {
        tax_value = state_wise_tax_classes[1482][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function deFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1483].length; i++) {
        tax_value = state_wise_tax_classes[1483][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function goFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1484].length; i++) {
        tax_value = state_wise_tax_classes[1484][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function guFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1485].length; i++) {
        tax_value = state_wise_tax_classes[1485][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function haFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1486].length; i++) {
        tax_value = state_wise_tax_classes[1486][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function hpFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1487].length; i++) {
        tax_value = state_wise_tax_classes[1487][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function jaFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1488].length; i++) {
        tax_value = state_wise_tax_classes[1488][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function kaFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1489].length; i++) {
        tax_value = state_wise_tax_classes[1489][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function keFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1490].length; i++) {
        tax_value = state_wise_tax_classes[1490][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function liFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1491].length; i++) {
        tax_value = state_wise_tax_classes[1491][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function mpFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1492].length; i++) {
        tax_value = state_wise_tax_classes[1492][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function maFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1493].length; i++) {
        tax_value = state_wise_tax_classes[1493][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function mnFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1494].length; i++) {
        tax_value = state_wise_tax_classes[1494][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function meFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1495].length; i++) {
        tax_value = state_wise_tax_classes[1495][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function miFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1496].length; i++) {
        tax_value = state_wise_tax_classes[1496][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function naFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1497].length; i++) {
        tax_value = state_wise_tax_classes[1497][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function orFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1498].length; i++) {
        tax_value = state_wise_tax_classes[1498][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function poFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1499].length; i++) {
        tax_value = state_wise_tax_classes[1499][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function puFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1500].length; i++) {
        tax_value = state_wise_tax_classes[1500][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function raFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1501].length; i++) {
        tax_value = state_wise_tax_classes[1501][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function siFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1502].length; i++) {
        tax_value = state_wise_tax_classes[1502][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function tgFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[4033].length; i++) {
        tax_value = state_wise_tax_classes[4033][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function tnFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1503].length; i++) {
        tax_value = state_wise_tax_classes[1503][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function trFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1504].length; i++) {
        tax_value = state_wise_tax_classes[1504][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function upFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1505].length; i++) {
        tax_value = state_wise_tax_classes[1505][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function wbFormatCombo(val) {
    var finalVal = [];
    if (val !== undefined) {
        $.each(val, function () {
            var temp_value = $(this)[0].text;
            finalVal.push(temp_value);
        });
    } else if (val === undefined) {
        val = '';
    }
    var i, tax_value;
    for (i = 0; i < state_wise_tax_classes[1506].length; i++) {
        tax_value = state_wise_tax_classes[1506][i];
        if (tax_value.text == finalVal) {
            finalVal = tax_value.value;
        }
    }
    return finalVal;
}

function urlfunction(oldItems, items, rowId) {
    var oldArray = new Array(), newArray = new Array(), token = $("#token_value").val();
    $.each(oldItems, function (i, obj) {
        oldArray.push(obj.data.text);
    });
    $.each(items, function (i, obj) {
        newArray.push(obj.data.text);
    });
    $.ajax({
        type: "POST",
        data: "oldArr=" + oldArray + "&newArr=" + newArray + "&rowId=" + rowId,
        url: "/tax/deletetaxmap?_token=" + token,
        success: function (data)
        {
            console.log(data);
//            $("#grid").igGrid("saveChanges");
            $("#grid").igGrid("dataBind");
        }
    });
}