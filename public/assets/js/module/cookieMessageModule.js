define(['jquery'], function($) {

    var cookiePolicyBar = function() {
        var document = window.document;
        var supportsTextContent = 'textContent' in document.body; // IE8 does not support textContent, so we should fallback to innerText.
        var cookieName = 'displayCookieConsent';
        var cookieConsentId = 'cookieChoiceInfo';
        var dismissLinkId = 'btn-close';
        var cookieText = 'Utilizziamo i cookie per offrire i nostri servizi. Utilizzando il nostro sito web acconsenti all\'utilizzo di cookie secondo la nostra politica di ';
        var cookieBtn = 'X';
        var cookieTitle = "privacy & policy.";
        var cookieUrl = rootUrl;

        function init() {
            showCookieConsentBar(cookieText,cookieBtn,cookieTitle,cookieUrl);
        }

        function _createHeaderElement(cookieText, dismissText, linkText, linkHref) {
            var butterBarStyles = '';

            var cookieConsentElement = document.createElement('div');
            cookieConsentElement.id = cookieConsentId;
            cookieConsentElement.className = 'cookie-bar';
            var inner = document.createElement('div');
            var cookieicon = document.createElement('div');
            //cookieicon.className = 'cookie-icon';
            inner.className = 'inner';
            inner.appendChild(_createConsentText(cookieText,linkText,linkHref));
            //cookieConsentElement.style.cssText = butterBarStyles;
            //cookieConsentElement.appendChild(_createConsentText(cookieText));
            //inner.appendChild(cookieicon);
            cookieConsentElement.appendChild(inner);


            inner.appendChild(_createDismissLink(dismissText));
            return cookieConsentElement;
        }

        function _createDialogElement(cookieText, dismissText, linkText, linkHref) {
            var glassStyle = 'position:fixed;width:100%;height:100%;z-index:999;' +
                'top:0;left:0;opacity:0.5;filter:alpha(opacity=50);' +
                'background-color:#ccc;';
            var dialogStyle = 'z-index:1000;position:fixed;left:50%;top:50%';
            var contentStyle = 'position:relative;left:-50%;margin-top:-25%;' +
                'background-color:#fff;padding:20px;box-shadow:4px 4px 25px #888;';

            var cookieConsentElement = document.createElement('div');
            cookieConsentElement.id = cookieConsentId;


            var glassPanel = document.createElement('div');
            glassPanel.style.cssText = glassStyle;

            var content = document.createElement('div');
            content.style.cssText = contentStyle;

            var dialog = document.createElement('div');
            dialog.style.cssText = dialogStyle;

            var dismissLink = _createDismissLink(dismissText);
            dismissLink.style.display = 'block';
            dismissLink.style.textAlign = 'right';
            dismissLink.style.marginTop = '8px';
            dismissLink.style.backgroundColor = "#fff";

            content.appendChild(_createConsentText(cookieText,linkText, linkHref));

            content.appendChild(dismissLink);
            dialog.appendChild(content);
            cookieConsentElement.appendChild(glassPanel);
            cookieConsentElement.appendChild(dialog);
            return cookieConsentElement;
        }

        function _setElementText(element, text,linkText,linkHref) {
            if (supportsTextContent) {
                element.textContent = text;
                if (!!linkText && !!linkHref) {
                    element.appendChild(_createInformationLink(linkText, linkHref));
                }
            } else {
                element.innerText = text;
                if (!!linkText && !!linkHref) {
                    element.appendChild(_createInformationLink(linkText, linkHref));
                }
            }
        }

        function _createConsentText(cookieText,linkText,linkHref) {
            var consentText = document.createElement('div');
            var p = document.createElement('div');
            p.className='wysiwyg';

            _setElementText(consentText.appendChild(p), cookieText,linkText,linkHref);
            return consentText;
        }

        function _createDismissLink(dismissText) {
            var dismissLink = document.createElement('a');
            _setElementText(dismissLink, dismissText);
            dismissLink.id = dismissLinkId;
            dismissLink.href = '#';
            //dismissLink.style.marginLeft = '24px';
            return dismissLink;
        }

        function _createInformationLink(linkText, linkHref) {
            var infoLink = document.createElement('a');
            _setElementText(infoLink, linkText);
            infoLink.href = linkHref;
            //infoLink.target = '_blank';
            //infoLink.style.marginLeft = '8px';
            infoLink.className += "";
            return infoLink;
        }

        function _dismissLinkClick() {
            _saveUserPreference();
            _removeCookieConsent();
            return false;
        }

        function _showCookieConsent(cookieText, dismissText, linkText, linkHref, isDialog) {
            if (_shouldDisplayConsent()) {
                _removeCookieConsent();
                var consentElement = (isDialog) ?
                    _createDialogElement(cookieText, dismissText, linkText, linkHref) :
                    _createHeaderElement(cookieText, dismissText, linkText, linkHref);
                var fragment = document.createDocumentFragment();
                fragment.appendChild(consentElement);
                document.body.appendChild(fragment.cloneNode(true));
                document.getElementById(dismissLinkId).onclick = _dismissLinkClick;
            }
        }

        function showCookieConsentBar(cookieText, dismissText, linkText, linkHref) {
            _showCookieConsent(cookieText, dismissText, linkText, linkHref, false);
        }

        function showCookieConsentDialog(cookieText, dismissText, linkText, linkHref) {
            _showCookieConsent(cookieText, dismissText, linkText, linkHref, true);
        }

        function _removeCookieConsent() {
            var cookieChoiceElement = document.getElementById(cookieConsentId);
            if (cookieChoiceElement != null) {
                cookieChoiceElement.parentNode.removeChild(cookieChoiceElement);
            }
        }

        function _saveUserPreference() {
            // Set the cookie expiry to one year after today.
            var expiryDate = new Date();
            expiryDate.setFullYear(expiryDate.getFullYear() + 1);
            document.cookie = cookieName + '=y; expires=' + expiryDate.toGMTString();
        }

        function _shouldDisplayConsent() {
            // Display the header only if the cookie has not been set.
            return !document.cookie.match(new RegExp(cookieName + '=([^;]+)'));
        }

        return {
            init: init
        }

    };

    return cookiePolicyBar;
});

