/*
	FUNZIONAMENTO

	Cerca nel DOM gli elementi che richiedono il caricamento di un js aggiuntivo.
	Se trova l'elemento, incrementa il COUNTER e esegue la funzione require per js richiesto.
	Quando il JS è stato caricato viene inserita la callback nell'array, e richiamato il CONTROLLER.
	Nel CONTROLLER: se il COUNTER è a zero (ovvero tutti i js sono stati caricati),
	viene fatto ciclo sull'array ed eseguite le callback.
	*/

/* isMobile */
window.isMobile = false;
(function (a) {
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) window.isMobile = true;
})(navigator.userAgent || navigator.vendor || window.opera);

/* isTablet */
window.isTablet = false;
if (!isMobile) {
    (function (a) {
        if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) window.isTablet = true;
    })(navigator.userAgent || navigator.vendor || window.opera);
}

var _DL = null;
var _DLA = null;
var SearchManager = null;
var CampaignManager = null;
var DealerFileManager = null;
var AzureBlobStorageManager = null;
var LibraryManager = null;
var DealerOrderManager = null;
var DiscussionBoardManager = null;
var PDFManager = null;
var DiscountManager = null;
var DealerDocumentManager = null;
var DealerDocumentDetailManager = null;
var DealerPromoManager = null;
var LocalCampaignManager = null;
var DealerEditorManager = null;
var MaterialEditManager = null;
var FileAzureManager=null;
var DealerPromoAdminManager = null;
var CustomLoadMoreManager = null;
var FileUploaderManager = null;
var xliffM = null;
var lightbox = null;
var ReportManager = null;
var counter = 1; // Conteggio dei require necessari
var editing = getElementsByClassName("ms-WPBody").length ? true : false;
var isOrigin = (document.location.hostname.indexOf("jstg") != -1) ? true : false;
var isLocal = (!isOrigin && (document.location.hostname.indexOf("www2") != -1 || location.href.indexOf('protonhag.cloud.reply.eu') != -1)) ? true : false;

var initExternalLibrary = function () {
    function controller() {
        counter--;

        if (counter > 0)
            return false;

        var classes = document.body.className;
        document.body.className = classes.split("loading").toString().replace(",", "");

        //IF IE8 REDRAW :AFTER/:BEFORE
        window.refreshIE8before = function () {
            if (!Modernizr.input.placeholder) {
                var head = document.getElementsByTagName('head')[0],
                    style = document.createElement('style');
                style.type = 'text/css';
                style.styleSheet.cssText = ':before,:after{content:none !important}';
                head.appendChild(style);
                setTimeout(function () {
                    head.removeChild(style);
                }, 0);
            }
        };
    };

    /* CUSTOM SELECT */
    if (document.getElementsByTagName("select").length && !editing) {
        counter++;
        require(["customSelect"], function () {
            createSelect();
            controller();
        });
    };

    /* CASE MATERIAL EDIT */
    if (getElementsByClassName("materialEdit").length) {
        counter++;
        require(["module/materialEdit"], function (me) {
            if (MaterialEditManager != null) return;
            MaterialEditManager = new me();
            MaterialEditManager.init();
            controller();
        });
		counter++;
        require(["module/FileAzureUploader"], function (faz) {
            if (FileAzureManager != null) return;
            FileAzureManager = new faz();
            controller();
        });
    };

    /* CASE ADD NEW CAMPAIGN */
    if (getElementsByClassName("centralCampaignSteps").length && getElementsByClassName("step-container").length || getElementsByClassName("sub-camp-table").length) {
        counter++;
        require(["module/centralCampaign"], function (cm) {
            if (CampaignManager != null) return;
            CampaignManager = new cm();
            CampaignManager.init();
            controller();
        });
    }

    /* CASE ADD NEW Local CAMPAIGN */
    if (getElementsByClassName("LocalCampaignSteps").length && getElementsByClassName("step-container").length) {
        counter++;
        require(["module/localCampaign"], function (cm) {
            if (LocalCampaignManager != null) return;
            LocalCampaignManager = new cm();
            LocalCampaignManager.init();
            controller();
        });
    }

    /* LIBRARY MANAGER */
    if (getElementsByClassName("LibraryManagerBlock").length) {
        counter++;
        require(["module/LibraryManager"], function (lm) {
            if (LibraryManager != null) return;
            LibraryManager = new lm();
            LibraryManager.init();
            controller();
        });
		counter++;
        require(["module/FileAzureUploader"], function (faz) {
            if (FileAzureManager != null) return;
            FileAzureManager = new faz();
            controller();
        });
    }


    /* CASE ADD NEW DEALER PROMO */
    if (getElementsByClassName("dealerPromoSteps").length && getElementsByClassName("step-container").length) {
        counter++;
        require(["module/dealerPromo"], function (cm) {
            if (DealerPromoManager != null) return;
            DealerPromoManager = new cm();
            DealerPromoManager.init();
            controller();
        });
    }

    /* CASE ADD NEW DEALER PROMO ADMIN */
    if (getElementsByClassName("promoAdmin").length) {
        counter++;
        require(["module/dealerPromoAdmin"], function (cm) {
            if (DealerPromoAdminManager != null) return;
            DealerPromoAdminManager = new cm();
            DealerPromoAdminManager.init();
            controller();
        });
    }

    /* CASE DEALER DISCUSSION BOARD */
    if (getElementsByClassName("DiscussionBoardBlock").length) {
        counter++;
        require(["module/DiscussionBoardManager"], function (db) {
            if (DiscussionBoardManager != null) return;
            DiscussionBoardManager = new db();
            DiscussionBoardManager.init();
            controller();
        });
    }

    /* CASE ADD NEW DEALER DOCUMENT */
    if (getElementsByClassName("dealerDocumentSteps").length && getElementsByClassName("step-container").length) {
        counter++;
        require(["module/dealerDocument"], function (cm) {
            if (DealerDocumentManager != null) return;
            DealerDocumentManager = new cm();
            DealerDocumentManager.init();
            controller();
        });
    }

    /* CASE DUPLICATE MY FILE */
    if (getElementsByClassName("MyFilesTable").length) {
        counter++;
        require(["module/dealerFile"], function (cm) {
            if (DealerFileManager != null) return;
            DealerFileManager = new cm();
            controller();
        });

        counter++;
        require(["module/PDFGenerator"], function (pdfm) {
            if (PDFManager != null) return;
            PDFManager = new pdfm();
            PDFManager.init();
            controller();
        });

        counter++;
        require(["module/lightbox"], function (LightBox) {
            createLightbox(LightBox);
            controller();
        });
    }

    /* PDF DOWNLOADER */
    if (getElementsByClassName("pdfdownloader").length) {
        counter++;
        require(["module/PDFGenerator"], function (pdfm) {
            if (PDFManager != null) return;
            PDFManager = new pdfm();
            PDFManager.init();
            controller();
        });
    }
    /* DISCOUNT MANAGER */
    if (getElementsByClassName("discountManagerBlock").length) {
        counter++;
        require(["module/DiscountManager"], function (dm) {
            if (DiscountManager != null) return;
            DiscountManager = new dm();
            DiscountManager.init();
            controller();
        });
    }

    /* AZURE BLOB STORAGE MANAGER */
    if (getElementsByClassName("AzureBlobStorageBlock").length) {
        counter++;
        require(["module/AzureBlobStorage"], function (dm) {
            if (AzureBlobStorageManager != null) return;
            AzureBlobStorageManager = new dm();
            AzureBlobStorageManager.init();
            controller();
        });
    }


	if (getElementsByClassName("loadJsonManager").length) {
        counter++;
        require(["module/JSONManager"], function () {
            controller();
        });
    }
    if (getElementsByClassName("loadJsonManagerArchive").length) {
        counter++;
        require(["module/JSONManagerArchive"], function (dla) {
			if (_DLA == null) {
                _DLA = new dla();
                _DLA.init();
            }
            controller();
        });
    }
    if (getElementsByClassName("JsondocumentLibraryLoader").length) {
        counter++;
        require(["module/JSONdocumentLibraryLoader"], function (dl) {
            if (_DL == null) {
                _DL = new dl();
                _DL.init();
            }
            controller();
        });
    }
    if (getElementsByClassName("JsondocumentLibraryLoaderAdmin").length) {
        counter++;
        require(["module/JSONdocumentLibraryLoaderAdmin"], function (dl) {
            if (_DL == null) {
                _DL = new dl();
                _DL.init();
            }
            controller();
        });
    }
	
	/* CASE SEARCH*/
	if (getElementsByClassName("JsonSearch").length) {
        counter++;
        require(["module/JsonSearch"], function (sm) {
			if (SearchManager == null) {
                SearchManager = new sm();
                SearchManager.init();
            }
			controller();
        });
    }


    /* CASE VIEW REPORT */
    if (getElementsByClassName("report").length) {
        counter++;
        require(["module/ViewReport"], function (rm) {
            if (ReportManager != null) return;
            ReportManager = new rm();
            controller();
        });

        counter++;
        require(["module/PDFGenerator"], function (pdfm) {
            if (PDFManager != null) return;
            PDFManager = new pdfm();
            PDFManager.init();
            controller();
        });
    }

    if (getElementsByClassName("MyOrdersTable").length) {
        counter++;
        require(["module/dealerOrder"], function (rm) {
            if (DealerOrderManager != null) return;
            DealerOrderManager = new rm();
            DealerOrderManager.init();
            controller();
        });

    }

    /**/
    if (getElementsByClassName("dealerDocumentDetail").length) {
        counter++;
        require(["module/DealerDocumentDetail"], function (rm) {
            if (DealerDocumentDetailManager != null) return;
            DealerDocumentDetailManager = new rm();
            DealerDocumentDetailManager.init();
            controller();
        });
    }
    if (getElementsByClassName("fileUploaderJs").length) {
        counter++;
        require(["module/FileUploader"], function (rm) {
            if (FileUploaderManager != null) return;
            FileUploaderManager = new rm();
            controller();
        });
    }

    /* CASE LIGHTBOX */
   // if (getElementsByClassName("lightBox-trigger").length || getElementsByClassName("JsondocumentLibraryLoader").length || getElementsByClassName("PortalReport").length || getElementsByClassName("LibraryManagerBlock").length) {
    if(true){
		counter++;
        require(["module/lightbox"], function (LightBox) {
            createLightbox(LightBox);
            controller();
        });
    }

    /* CASE SPACCA-LA-PAGINA */
    if (getElementsByClassName("toBeOpened").length) {
        counter++;
        require(["module/miscellanea"], function (miscellanea) {
            createMiscellanea(miscellanea);
            controller();
        });
    }

    /* CASE SUBMENU */
    /*if (getElementsByClassName("second-nav").length && !editing)
    {
        counter++;
        require(["module/menuUtils"], function(menuUtils)
        {
            createSubmenu(menuUtils);
            controller();
        });
    }*/

    /* CASE FORM */

    if (getElementsByClassName("form").length) {
        counter++;
        require(["jquery", "Validate", "form"], function (formValidate) {
            validaform(formValidate);
            controller();
        });
    }

    /* CASE MULTISELECTION */
    if (getElementsByClassName("multi-selection-element").length) {
        counter++;
        require(["module/multiselection"], function (Multiselection) {
            createMultiselection(Multiselection);
            controller();
        });
    }

    /* CASE SLIDESHOW */
    if ((temp = getElementsByClassName("slideshow-mask", "*", "libraryLoaded")).length) {
        counter++;
        (function (temp) {
            require(["module/slideshow"], function (Slideshow) {
                createSlideshow(Slideshow, temp);
                controller();
            });
        })(temp);
    }


    if (getElementsByClassName("history-timeline").length) {
        counter++;
        require(["module/timeline"], function () {
            controller();
        });
    }


    if (getElementsByClassName("team-item").length) {
        counter++;
        require(["module/team-description"], function () {
            controller();
        });
    }

    /* calculator */

    if (getElementsByClassName("calculatorwrapper").length) {
        counter++;
        require(["module/calculators"], function () {
            controller();
        });
    }

    // CASE JQUERY-UI CALENDAR
    if (getElementsByClassName("datepicker").length) {
        counter++;
        require(["module/calendar"], function () {
            //createEvents();
            //alert('ok');
            controller();
        });
    }

    /* BITLAZYLOADING
    if (typeof (_LLBit) === 'undefined') {
        counter++;
        require(["module/bitLazyLoading"], function (BitLazyLoading) {
            controller();
            _BitLL.showImages();
            $(window).add("#s4-workspace").on("scroll", function () {
                _BitLL.showImages();
            });
        });
    }*/

    /* CASE PDF FORM - CUSTOM LOAD MORE */
    if (getElementsByClassName("CustomLoadMore").length) {
        counter++;
        require(["module/customLoadMore"], function (customLoadMore) {
            if (CustomLoadMoreManager != null) return;
            CustomLoadMoreManager = new customLoadMore();
            CustomLoadMoreManager.init();
            controller();
        });
    }

	if (getElementsByClassName("translations").length) {
        counter++;
        require(["module/XLIFFManager"], function (xliffManager) {
            if (xliffM != null) return;
            xliffM = new xliffManager();
            xliffM.init();
            controller();
        });
    }
	    /* CASE DEALER EDITOR */
    if (getElementsByClassName("dealerEditor").length) {
        counter++;
        require(["module/DealerEditor"], function (de) {
            if (DealerEditorManager != null) return;
            DealerEditorManager = new de();
            DealerEditorManager.init();
            controller();
        });
    }
	
    controller();
};

initExternalLibrary();
//  ---------------------------------------------------------------------------
//  REQUIRE CALLBACKS (le più semplici vengono passate direttamente nel require)


//  SELECT
function createSelect() {
    $val = $("select").find('option:first-child').attr('value');
    $("select").not(".libraryLoaded, .hidden-select, prefilled").val($val).addClass("libraryLoaded").customSelect();
    $(".customStyleSelectBox").addClass("arrow-down-black");
}

//  MENU
function createMenu(menu) {
    if (!$('#mainheader').hasClass('libraryLoaded')) {
        var m = new menu();
        m.init();
    }
}

function createGallery(Gallery) {
    var gallery = new Gallery();
    gallery.init();
}

//  SUBMENU
function createSubmenu(menuUtils) {
    var nav = $(".second-nav").not('.libraryLoaded'),
		mu = new menuUtils();
    mu.init(
	{
	    menu: nav.find('a')
	});
}

//  LIGHTBOX
function createLightbox(LightBox) {
    lightbox = new LightBox();
    lightbox.init();
}

// Form autocomplete
function fillInForm() {
    var data = getQuerystring("data"),
		temp;
    data = decodeURIComponent(data).split("||");
    for (var i in data) {
        if (data[i].indexOf('|') == 0)
            data[i] = data[i].substring(1);
        temp = data[i].split("|");
        if (temp.length == 2)
            $('[name="' + temp[0] + '"]').val(temp[1]);
    }
}

//  FORM
function createForm(FormManager) {
    if ($(".form").parents("section").not(".libraryLoaded").addClass("libraryLoaded").length > 0) {
        var elem = $(".form").parents("section"),
			option = {};
        if (elem.attr('data-obj') !== undefined)
            option = $.parseJSON(elem.attr('data-obj'));
        else
            option = $.parseJSON($(".special-offers-detail").find('*[data-obj]').eq(0).attr('data-obj'));

        var form = new FormManager();
        form.init(option);
    }
}

function validaform(formValidate) {
    var form = new formValidate();
    form.init();
}

// NEW FORM MANAGER
function createFormManager(FormManager) {
    var elems = getElementsByClassName("form-container"),
		formData;

    for (var elemItem = elems.length; elemItem--;) {
        var elem = $(elems[elemItem]),
			option = {};
        if (!elem.hasClass('libraryLoaded')) {
            elem.addClass('libraryLoaded');
            if (elem.attr('data-obj') !== undefined)
                option = $.parseJSON(elem.attr('data-obj'));
            else
                option = $.parseJSON($(".special-offers-detail").find('*[data-obj]').eq(0).attr('data-obj'));

            formData = option.vars !== undefined ? window[option.vars] : formVars;

            if (typeof (formData) != "undefined") {
                if (typeof (formData.validationFunctions) != "undefined")
                    option.validationFunctions = formData.validationFunctions;

                if (typeof (formData.validationMessages) != "undefined")
                    option.validationMessages = formData.validationMessages;

                if (typeof (formData.endpointUrlGold) != "undefined")
                    option.endpointUrlGold = formData.endpointUrlGold;

                if (typeof (formData.endpointUrlProd) != "undefined")
                    option.endpointUrlProd = formData.endpointUrlProd;

                if (typeof (formData.rules) != "undefined")
                    option.rules = formData.rules;

                if (typeof (formData.formLabels) != "undefined")
                    option.formLabels = formData.formLabels;

                if (typeof (formData.flowManagerUrl) != "undefined")
                    option.flowManagerUrl = formData.flowManagerUrl;

                if (typeof (formData.submitCallback) != "undefined")
                    option.submitCallback = formData.submitCallback;
            }
            var form = new FormManager();
            form.init(option);
        }
    }
}

//  SLIDESHOW

function createSlideshow(Slideshow, elems) {
    /*var elems = getElementsByClassName("slideshow-mask");*/
    for (var elemItem = elems.length; elemItem--;) {
        var t = $(elems[elemItem]).addClass('libraryLoaded');

        if (t.hasClass("mobile-disabled") && isMobile)
            return false;

        var s = new Slideshow(),
			objFull = {},
			objQueryelementsContainer,
			objQueryBottom = t.find('.bottom-tape').length ?
			{
			    consoleContainer: t.find('.bottom-tape')
			} : undefined;

        var objVideoSettings = t.find('.audioOn').length ?
		{
		    audioOn: true
		    //controls: 1
		} :
		{};
        var objQuerydots = t.find('.dots').length ?
		{
		    dots: t.find('.dots')
		} : undefined;

        var objQueryArrows = t.find('.bottom-tape .arrows').length ?
		{
		    arrow: t.find('.bottom-tape .arrows')
		} : undefined;

        objQueryelementsContainer = t.find('.elemContainer').length ?
		{
		    slideContainers: t.find('.elemContainer')
		} : undefined;

        var objDom = t.attr('data-obj') !== undefined ? JSON.parse(t.attr('data-obj')) : '',
			obj = {
			    slideshowMask: t,
			    stopOnHover: false
			};
        if (objDom.auto == true) {
            if (t.parents('.main_canvas').length) {
                objDom.continuous = true;
                objVideoSettings.controls = 0;
            }
            (function (s) {
                $(document).on('openMenuComplete', function () {
                    s.stop(true);
                });
            })(s);
            (function (s) {
                $(document).on('closeMenuComplete', function () {
                    s.restart(5000);
                });
            })(s);
        }
        var objInstance = { slideshowIstance: s };

        objFull = $.extend(obj, objDom, objQueryBottom, objQueryArrows, objQuerydots, objQueryelementsContainer, objVideoSettings, objInstance);
        s.init(objFull);
    }
}

//  MULTISELECTION
function createMultiselection(Multiselection) {
    var elem = $(".multi-selection-element").not('.libraryLoaded').addClass('libraryLoaded');
    for (var elemItem = elem.length; elemItem--;) {
        var p = $(elem[elemItem]),
			objDom = p.attr('data-obj') !== undefined ? $.parseJSON(p.attr('data-obj')) : '';

        objDom.tabChangedCb = eval(objDom.tabChangedCb);
        var obj = new Multiselection();
        obj.init(objDom, elem[elemItem]);
        p.data("instance", obj);
    }
}

// MISCELLANEA
function createMiscellanea(Miscellanea) {
    var m = new Miscellanea.openOneElement();

    m.init();
}


/* Fix per Safari Mobile */

(function (basePath) {
    var ua = navigator.userAgent.match(/AppleWebKit\/(\d*)/),
		css = "";
    if (ua && ua[1] < 535) {
        document.body.className += " safariFix";
        css = "@font-face {font-family: 'safariFix';src: url('" + basePath + "safariFix.svg#safariFix') format('svg');font-weight: normal;font-style: normal;}";
    }
    if (!Modernizr.mediaqueries) {
        css += ".mobile-visible,.tablet-visible,.desktop-hidden{display: none !important;}";
    }
    var head = document.getElementsByTagName('head')[0],
		s = document.createElement('style');
    s.setAttribute('type', 'text/css');
    if (s.styleSheet) // IE
        s.styleSheet.cssText = css;
    else // the world
        s.appendChild(document.createTextNode(css));
    head.appendChild(s);
})('..Style%20Library/css/');

/* */


/* MAIN JS */
require(['jquery'], function ($) {
    // per gestire il caricamento di altre righe della tabella
    var loadmore = {
        nextIndex: 0,
        totalItem: 0
    };

    /*$(window).load(function() {

	});
*/

    $(document).ready(function () {
		try
		{
			if($.trim($("span.user-name").text()) == "")
			{
				
				var tUser = $.trim($("#currentUser").text()).split('|');
				if(tUser.length==2)
					$("span.user-name").text(tUser[1]).css("max-width","100%");
				else
					$("span.user-name").text($.trim($("#currentUser").text())).css("max-width","100%");
			}
		}
		catch(ex){}
		$("div.search-bar a.send").on('click', function () {
			var inputs = $(this).prev();
			document.location.href = GlobalBaseUrl + getCurrentBrand() + "/Pages/Search.aspx?q=" + inputs.val();
        });

        $(".discountManagerBlock input[type='radio']").on('click', function () {
            // alert($(this).val());
            $(".discountManagerBlock .discVal").attr("disabled", "disabled");
            $(this).parent().find(".discVal").removeAttr("disabled");
        });


        if ($(".status_big").length > 0)
            calculateStatus();


        $('.step-container').on("click", '.next-step', function () {
            $(this).closest('.step-container').find('.step:visible').fadeOut(1000, function () { $(this).next('.step').fadeIn(1000); $('.steps-bar .active').next().addClass('active'); destroyScroll(); customScroll(); });
            //$(this).closest('.step-container').find('.step:visible').fadeOut(500).next('.step').fadeIn(1500);
            return false;
        });

        /* Custom radio/checkbox */
        $('body').on("click", ".checkbox label > *, .radiobutton label, .radiobutton label > span", function (e) {
            changeButton($(this).parent('label'));
        });
        $('body').on("click", ".radiobutton label", function (e) {
            changeButton($(this));
        });
        $('body').on("click", ".checkbox label a, .radiobutton label a", function (e) {
            e.stopPropagation();
        });
        // Verifico che le check/radio sono siano già checked per qualche memoria del browser
        $.each($('.radiobutton label, .checkbox label'), function (index, value) {
            ($(this).siblings('input').is(":checked")) ? $(this).addClass("checked") : $(this).remove("checked");
        });
        /* end Custom Radio */

        /* sliding-content */
        $('.slide-trigger').on("click", function (e) {
            e.stopPropagation();
            $(this).toggleClass('active').parent('.sliding-content').find('.content-hidden').slideToggle(300).toggleClass('open');
            e.preventDefault();
        });
		
        /* accordion-menu */
        $('.accordion-menu-standard').each(function (e) {
            $menu = $(this);
            $openItem = $menu.data('open');
            isMultiple = $menu.data('multiple');
            $menu.find('li > .hasSubMenu').on("click", function (e) {
                e.stopPropagation();
                $trigger = $(this).closest('li');
                if (!isMultiple) {
                    $(this).parents('li').siblings('.open').removeClass('open').find('> ul').slideToggle(300)
					.find('.open').removeClass('open').find('> ul').slideToggle(300).find('a.active').removeClass('active');
                }
                $trigger.toggleClass('open').find(' > ul').slideToggle(300);
            });
            if ($openItem && $openItem > 0)
                $menu.find('> li').eq($openItem - 1).find('> .hasSubMenu').trigger('click');
        });

        $('.accordion-menu-library').each(function (e) {
			
			// Remove first breadcrumb chunk
			$("#breadcrumbs ul li:nth-child(2)").remove();
			
            $menu = $(this);
            $openItem = $menu.data('open');
            isMultiple = $menu.data('multiple');

            $menu.on("click", 'li > .hasSubMenu', function (e) {
				//modifica librarymanager per archive
				var temp = $(this).closest('li').parent().parent().attr("name");
				if(typeof temp != "undefined")
					temp = temp.split("/");
				else
					temp=[""];
				gallery_filterDL =  $(this).closest('li').find('a:first').text() + "|" +  temp[temp.length-1];
				//gallery_filterDL =  $(this).closest('li').find('a:first').text() + "|" +  $(this).closest('li').parent().parent().find('a:first').text();
                e.stopPropagation();
                $trigger = $(this).closest('li');
                if (!isMultiple) {
                    $(this).parents('li').siblings('.open').removeClass('open').find('> ul').slideToggle(300).find('.open').removeClass('open').find('> ul').slideToggle(300);
					$menu.find('a.active').removeClass('active');					
					$(this).parent('li').find('> ul').find('.open').removeClass('open').find('> ul').slideToggle(300);
					
                }
				if(!$trigger.hasClass('open'))
				{
					$trigger.toggleClass('open').find(' > ul').slideToggle(300);					
				}

                e.preventDefault();

					if (_DL == null)
					{
						require(["module/JSONdocumentLibraryLoader"], function (dl) {
							_DL = new dl();
							_DL.init();
							_DL.setCurrentItem($trigger.attr("fid"));
						});
					}
					else
						_DL.setCurrentItem($trigger.attr("fid"));

            });

            if ($openItem && $openItem > 0) {
                $menu.find('> li').eq($openItem - 1).find('> .hasSubMenu').trigger('click');
            }
        });
		
		$('.accordion-menu-library-archive').each(function (e) {
            $menu = $(this);
            $openItem = $menu.data('open');
            isMultiple = $menu.data('multiple');

            $menu.on("click", 'li > .hasSubMenu', function (e) {
				var temp = $(this).closest('li').parent().parent().attr("name");
				if(typeof temp != "undefined")
					temp = temp.split("/");
				else
					temp=[""];
				gallery_filterDL =  $(this).closest('li').find('a:first').text() + "|" +  temp[temp.length-1];
				//gallery_filterDL =  $(this).closest('li').find('a:first').text() + "|" +  $(this).closest('li').parent().parent().find('a:first').text();
                e.stopPropagation();
                $trigger = $(this).closest('li');
                if (!isMultiple) {
                    $(this).parents('li').siblings('.open').removeClass('open').find('> ul').slideToggle(300)
					.find('.open').removeClass('open').find('> ul').slideToggle(300).find('a.active').removeClass('active');
                }
                $trigger.toggleClass('open').find(' > ul').slideToggle(300)/*.toggleClass('open')*/;
                e.preventDefault();
                /**/
                if ($trigger.hasClass("open"))
				{
					// 20150705 BC
					if (_DLA == null) 
					{
						require(["module/JSONManagerArchive"], function (dla) {
							_DLA = new dla();
							_DLA.init();
							_DLA.setCurrentItem($trigger.attr("fid"));
						});
					}
					else // 20150705 BC END
						_DLA.setCurrentItem($trigger.attr("fid"));
				}
                else
				{
					// 20150705 BC
					if (_DLA == null) 
					{
						require(["module/JSONManagerArchive"], function (dla) {
							_DLA = new dla();
							_DLA.init();
							_DLA.setCurrentItem($trigger.parents(".open").attr("fid"));
						});
					}
					else // 20150705 BC END
						_DLA.setCurrentItem($trigger.parents(".open").attr("fid"));
				}
            });

            if ($openItem && $openItem > 0) {
                $menu.find('> li').eq($openItem - 1).find('> .hasSubMenu').trigger('click');
                //$(".levelfirst").empty();
            }
        });

        $('.nav-title-Link').on("click", function (e) {
            $(".accordion-menu-library .open").not('.levelfirst').toggleClass('open').find(' > ul').slideToggle(300);
            _DL.setCurrentItem(null);
            $(".accordion-menu .menuitem a.active").removeClass("active");
			location.hash = "0";
        });


        /* filter-menu */
        $('.filter-menu').each(function (e) {
            $menu = $(this);
            $menu.on("click", 'li > .notHasSubMenu', function (e) {
                $menu.find('a.active').removeClass('active');
                $(this).addClass('active');
				//modifica librarymanager per archive
				var temp = $(this).closest('li').parent().parent().attr("name");
				if(typeof temp != "undefined")
					temp = temp.split("/");
				else
					temp=[""];
				gallery_filterDL =  $(this).closest('li').find('a:first').text() + "|" +  temp[temp.length-1];
				//gallery_filterDL =  $(this).closest('li').find('a:first').text() + "|" +  $(this).closest('li').parent().parent().find('a:first').text();				
                _DL.setCurrentItem($(this).closest('li').attr("fid"));
                e.preventDefault();
            });
        });
		
		$('.filter-menu-archive').each(function (e) {
            $menu = $(this);
            $menu.on("click", 'li > .notHasSubMenu', function (e) {
                $menu.find('a.active').removeClass('active');
                $(this).addClass('active');
				var temp = $(this).closest('li').parent().parent().attr("name");
				if(typeof temp != "undefined")
					temp = temp.split("/");
				else
					temp=[""];
				gallery_filterDL =  $(this).closest('li').find('a:first').text() + "|" +  temp[temp.length-1];
			//	gallery_filterDL =  $(this).closest('li').find('a:first').text() + "|" +  $(this).closest('li').parent().parent().find('a:first').text();
                _DLA.setCurrentItem($(this).closest('li').attr("fid"));
                e.preventDefault();
            });
        });

        $("#orderFilter").change(function (e) {
            _DL.changeOrderFilter($(this).val());
            e.preventDefault();
        });

        $(document).on("click", ".closeIframeLightbox", function (e) {
            //e.preventDefault();
            window.parent.$(".velina").trigger("click");
            e.stopPropagation();
        });





        $(document).on("click", ".myCustomTrigger", function (e) {
            $(this).find(".accordion-trigger").closest('tr').siblings('tr').find('.active').trigger('click');
            $tr = $(this).find(".accordion-trigger").toggleClass('active').closest('tr').next('tr');
            $tr.toggleClass('open').find('.active').trigger('click');
            $tr.find(".nested-table").toggleClass('active').find('tbody').toggleClass('open');
			$("#page-content > header > div.block.d24.text").css("height","101%");
			$("#page-content > header > div.block.d24.text").css("height","100%");
        });


        $(document).on("click", ".confirmUploadIframeLightbox", function (e) {
            if ($(this).hasClass("disabled")) {
                e.preventDefault(); e.stopPropagation(); return;
            }

            FileUploaderManager.excelUpload(getQuerystring("b"), getQuerystring("type"));
        });


        /* content hidden/visible */
        $('.content-hidden > .trigger').on("click", function (e) {
            $(this).toggleClass('active').next('div').toggleClass('hidden');
            e.preventDefault();
        });

        /* HEADER SEARCH */
        $('header .search-trigger').on("click", function (e) {
            $(this).toggleClass('active');
            $('header .search-bar').slideToggle();
            e.preventDefault();
        });


        /* #wrapper click */
        $('#wrapper').on("click", function (e) {
            $('.slide-trigger.active').trigger('click');
        });

        /* toolbox-table */
        $('.toolbox-table').not('.print-results-table').each(function () {
            $table = $(this);
            visibleRow = $table.data('visible');
            if (visibleRow) {
                $table.find('tbody tr').eq(visibleRow).nextAll().addClass('hidden-content');
            }
            if (!visibleRow || $table.find('tbody tr').length - 1 <= visibleRow) {
                $table.find('tfoot').addClass('hidden');
            }
        });


        $('.sub-camp-table').on('change', '.td-revisor select', function (e) {
            if ($(this).val() != "")
                $(this).closest('.block').addClass('valorized');
            else
                $(this).closest('.block').removeClass('valorized');
            if ($('.sub-camp-table .td-revisor .valorized.visible').length)
                $('.btn-done:visible').removeClass('disabled');
            else
                $('.btn-done:visible').addClass('disabled');
        });

        /* selectable items */
        $('.block').on("click", '.selectable', function (e) {
            $(this).toggleClass('selected').siblings('.selectable').removeClass('selected');
            if ($(this).hasClass("selected"))
                $('.confirmIframeLightbox').removeClass("disabled");
            else
                $('.confirmIframeLightbox').addClass("disabled");
            e.preventDefault();
        });

        /* removable items */
        $('.removable .close').on('click', function (e) {
            $removable = $(this).closest('.removable');
            $container = $(this).closest('.removable-container');
            $length = $(this).closest('.removable-container').find('.removable').length;
            $removable.fadeOut(1000, function () {
                if ($length == 1)
                    $container.addClass('empty');
                $removable.remove();
            });
            e.preventDefault();
        });

        /* upload content */
        $('.block-file-browse .form-button, .block-file-browse .btn-browse, .block-file-browse .fakeFileInput').on("click", function (e) {
            $input = $(this).closest('.block-file-browse').find('input[type=file]');
            $input.find($input.trigger('click'));
            e.preventDefault();
        });

        /* input datepicker */
        $('input.datepicker').on("click", function (e) {
            $(this).parent().find('button').trigger('click');
            e.preventDefault();
        });

        /* input file upload */
        $('.block-file-upload input').on("click", function (e) {
            $(this).closest('.block-file-upload').find('.btn-file').trigger('click');
            e.preventDefault();
        });

        $('.fakeFileInput').val('');

        $('.block-file-browse input[type=file]').change(function (e) {
            $val = $(this).val()
            $(this).closest('.block-file-browse').find('.fakeFileInput').val($val);
        });



        /* SEE MORE */
        $('.hidden-container').on('click', '.see-more', function (e) {
            $this = $(this);
            $max = $this.closest('.hidden-container').data('max');
            //console.log($max)
            $this.parents('.hidden-container').toggleClass('show-content');

            if (!$this.is('.active')) {
                $this.parents('.hidden-container').find('.hidden-content').fadeIn().css('display', 'inline-block');
            }
            else {
                $this.parents('.hidden-container').find('.hidden-content').fadeOut().css('display', 'none');
            }
            $this.toggleClass('active');
            var oldText = $this.text();
            $this.text($this.attr('data-secondText'));
            $this.attr('data-secondText', oldText);

            e.preventDefault();
        });

        /* my-files-table */
        if ($("#myTableFile").length) {

            $('#myTableFile').each(function () {
                $table = $(this);
                visibleRow = $table.data('visible');
                if (visibleRow) {
                    $table.find('tbody tr').eq(visibleRow - 1).nextAll().addClass('hidden-content');
                }
                if (!visibleRow || $table.find('tbody tr').length - 1 + $table.find('thead tr').length <= visibleRow) { $table.find('tfoot').addClass('hidden'); }
                else { $table.find('tfoot').removeClass('hidden'); }
            });
        }

        /* Footer select language */
        $('footer .select-language').on('click', 'a', function (e) {
            $(this).closest('li').addClass('selected').siblings('.selected').removeClass('selected');
        });

        /* Tooltips */
        $('.block-vt header').each(function () {
            var title = $(this).find('.title.prod-name');
            var tooltip = $(this).find('.tooltip');
            if ((title.prop("offsetWidth") < title.prop("scrollWidth"))) {
                title.addClass('overflowing');
            }
            else if (!tooltip.find('p').length)
                tooltip.hide();
        });


        // initialize scrollbar
        //customScroll();

        // remove click on disabled items
        //checkDisabledLink();

        // after docReady
        //docReadyInit();

    });

    function checkDisabledLink() {
        $('#wrapper').on("click", 'a', function (event) {
            if ($(this).attr('disabled') != undefined || $(this).is('.disabled')) {
                event.preventDefault();
            }
        });
    }
	
	function setImage(inputRef,imgSrc){
		inputRef.removeClass("emptyInput");
		var imgRef = inputRef.next();
		imgRef.css({"width":"auto","height":"auto"});
		imgRef[0].src = imgSrc;
		return false;
	}
       
    function checkToTop() {
        var height = $(window).scrollTop();
        if (height > 600)
            $('.toTop').fadeIn();
        else
            $('.toTop').fadeOut();
    }

    /* placeholder crossbrowser */
    if (!Modernizr.input.placeholder) {
        $('input, textarea').each(function () {
            /*if($(this).is('[type="password"]') ) {
				$(this).prop('type', 'text');
			}*/
            if ($(this).attr('placeholder') !== undefined && !$(this).is(':disabled')) {
                var value;
                $(this).attr('value', $(this).attr('placeholder')).on('focus', function () {
                    value = $(this).attr('value');
                    if ($(this).attr('value') == $(this).attr('placeholder')) {
                        $(this).attr('value', '');
                    }
                    //$(this).attr('value','');
                })
				.on('blur', function () {
				    if ($(this).val() === "")
				        $(this).attr('value', $(this).attr('placeholder'));
				});
            }
        });
    }

    var resizeTimer;
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function (argument) {
            //
        }, 200);
    });

    return false;

    function changeButton(label) {
        var labelEl = label,
			nameCheckbox = labelEl.attr("for"),
			name = labelEl.siblings("input").attr("name"),
			input_id = labelEl.siblings("input").attr("id");

        if (labelEl.siblings("input").is(":disabled"))
            return false;

        if (labelEl.siblings("input").is("[type='checkbox']")) {

            if (labelEl.hasClass("checked")) {
                labelEl.removeClass("checked");
                labelEl.siblings("input").attr("checked", false);
            }
            else {
                labelEl.addClass("checked");
                labelEl.siblings("input").attr("checked", true);
            }
            labelEl.find("span").css("z-index", "0");
        }
        else {
            $("input[name='" + name + "']").removeAttr("checked")
				.parents(".radio-button-container").find("label").removeClass("checked");
            labelEl.addClass("checked");
            $("input[id=" + nameCheckbox + "]").attr("checked", true);
        }

        /*-----------------------------
		 *  BEHAVIOUR per CONTACTS
		 *----------------------------- */
        if ($("#how-to-contacts").length > 0) {
            var id = "#" + input_id.split("-")[1],
				how = $("#how-to-contacts");

            how.find(".optional").hide().find(".required").removeClass('required');
            how.find(id).show().find("input, textarea, select").addClass('required');

            // Se il form non è valido e cambio, resetto tutto.
            how.parent(".form").data().validator.resetForm();
        }
        // END
    }
});

/*

var customScroll = function () {
    require(["mousewheel", "mwheel", "customScrollbar"], function () {
        $('.customScroll:visible').removeAttr("style").jScrollPane({
            showArrows: false,
            autoReinitialise: true
        });
        $('.table-container.customScroll:visible').bind(
            'jsp-scroll-x',
            function (event, scrollPositionX, isAtLeft, isAtRight) {
                if (isAtRight == true)
                    $(this).addClass('atRight')
                else
                    $(this).removeClass('atRight')
            }
        )
    });
};
var destroyScroll = function () {
    require(["mousewheel", "mwheel", "customScrollbar"], function () {
        var apis = [];
        $('.customScroll:visible').not('.empty').each(
            function () {
                apis.push($(this).jScrollPane().data().jsp);
            }
        )
        if (apis.length) {
            $.each(
                apis,
                function (i) {
                    this.destroy();
                }
            )
            apis = [];
        }
    });
};
var docReadyInit = function () {

    // overflowing content
    $('.block-vt .info-details > li').each(function (e) {
        $this = $(this);
        if ($this.outerHeight() < $this.prop('scrollHeight') - 5 || $this.outerWidth < $this.prop('scrollWidth')) {
            $this.addClass('overflowing');
        }
        else {
            $this.removeClass('overflowing');
        }

        $this.on('click', '> div > label', function (e) {
            $(this).closest('.overflowing').toggleClass('open');
        });
    });

    // check if custom checkbox are checked
    $('input[type="checkbox"]').not('.preselected').attr("checked", false);
    $('input[type="checkbox"]:checked').each(function () {
        $(this).next('label:not(.checked)').addClass('checked')
    });
};

*/