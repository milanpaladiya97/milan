jQuery(function(){
    var $activationSection = jQuery("#mcafeesecure-activation");
    var $dashboardSection = jQuery("#mcafeesecure-dashboard");

    var $sideframeSection = jQuery("#mcafeesecure-sideframe");
    var $proSection = jQuery("#mcafeesecure-pro");
    var $engagementSection = jQuery("#mcafeesecure-engage");
    var $logoSection = jQuery("#ts-logo-noframe");

    var $data = jQuery("#mcafeesecure-data");
    var host = $data.attr('data-host');
    if(!host){ host = '';}
    var email = $data.attr('data-email');
    if(!email){ email = '';}

    var endpointUrl = 'https://www.trustedsite.com';
    var apiUrl = endpointUrl + '/rpc/ajax?do=lookup-site-status&host=' + encodeURIComponent(host)
    var upgradeUrl = endpointUrl + "/user/site/" + host + "/upgrade";
    var setupUrl = endpointUrl + "/user/site/" + host + "/setup";
    var diagnosticsUrl = endpointUrl + "/user/site/" + host + "/diagnostics";
    var sipUrl = endpointUrl + "/user/site/" + host + "/sip";
    var profileUrl = endpointUrl + "/user/site/" + host + "/sitemap";
    var securityUrl = endpointUrl + "/user/site/" + host + "/mcafee-secure-security";
    var floatingTrustmarkUrl = endpointUrl + "/user/site/" + host + "/mcafee-secure-floating";
    var directoryUrl = endpointUrl + "/user/site/" + host + "/directory";
    var searchHighlightingUrl = endpointUrl + "/user/site/" + host + "/mcafee-secure-search";
    var engagementTrustmarkUrl = endpointUrl + "/user/site/" + host + "/mcafee-secure-engagement";

    jQuery("#activate-now").click(function(){
        var email_input = jQuery('#email-input').val();
        var domain_input = jQuery('#domain-input').val();        
        var left = window.innerWidth / 2 - 250;
        var top = 200;
        var signupUrl = endpointUrl + "/app/partner2/signup?ctx=popup&host=" + encodeURIComponent(domain_input) + "&email=" + encodeURIComponent(email_input) + "&aff=221269&platform=5";
        var signupWindow = window.open(signupUrl, "_blank");
    });

    // McAfee SECURE
    function renderSecurity(data){
        var issuesFound = data['diagnosticsFoundIssues'] === 1;
        var $row = jQuery("#security");
        var secure = data['isSecure'] === 1;
        var inProgress = data['scanInProgress'] === 1;

        if(inProgress){
            setLinkText($row, "Security scan in progress..." );
            spinIcon($row);
        }else{
            if(secure){
                setLinkText($row, "All tests passed" );
                checkIcon($row);
            }else{
                setLinkText($row, "Security issues");
                warningIcon($row);
            }   
        }
        setLinkHref($row, securityUrl);
    }

    function renderFloatingTrustmark(data){
        var pro = data['isPro'] === 1;
        var $row = jQuery("#floating-trustmark");
        var exceeded = data['maxHitsExceeded'] === 1;

        if(pro){
            setLinkText($row, "Active");
            checkIcon($row);
            setLinkHref($row, floatingTrustmarkUrl);
        }else{
            if(exceeded){
                setLinkText($row, "Monthly vistitor limit reached");
                timesIcon($row);
                setLinkHref($row, upgradeUrl);
            }else{
                setLinkText($row, "Active");
                checkIcon($row);
                setLinkHref($row, floatingTrustmarkUrl);
            }
        }
    }

    function renderDirectoryListing(data){
        var pro = data['isPro'] === 1;
        var inProgress = pro && !data['sitemapCreatedDate'];
        var $row = jQuery("#directory-listing");

        if(pro){
            if(inProgress){
                spinIcon($row);
                setLinkText($row, 'Indexing in progress');
            }else{
                checkIcon($row);
                setLinkText($row, "Update");
                }
            setLinkHref($row, directoryUrl);
        }else{
            setLinkText($row, "Upgrade");
            lockIcon($row);
            setLinkHref($row, upgradeUrl);
        }
    }

    function renderSearchHighlighting(data){
        var pro = data['isPro'] === 1;
        var $row = jQuery("#search-highlighting");

        if(pro){
            setLinkText($row, "Active");
            checkIcon($row);
            setLinkHref($row, searchHighlightingUrl);
        }else{
            setLinkText($row, "Upgrade");
            lockIcon($row);
            setLinkHref($row, upgradeUrl);
        }
    }

    function renderEngagementTrustmark(data){
        var pro = data['isPro'] === 1;
        var $row = jQuery("#engagement-trustmark");

        if(pro){
            var installed = data['tmEngagementInstalled'] === 1;
            if(installed){
                setLinkText($row, "Active");
                checkIcon($row);
            }else{
                setLinkText($row, "Add now");
                circleIcon($row);     
            }
            setLinkHref($row, engagementTrustmarkUrl);
        }else{
            setLinkText($row, "Upgrade");
            lockIcon($row);
            setLinkHref($row, upgradeUrl);
        }
    }

    function renderSip(data) {
        var pro = data['isPro'] === 1;
        var $row = jQuery("#sip");

        if(pro){
            var sipEnabled= data['sipEnabled'] === 1;

            if(sipEnabled){
                setLinkText($row, "Active");
                checkIcon($row);
                setLinkHref($row, sipUrl);
            }else{
                setLinkText($row, "Inactive");
                circleIcon($row);
                setLinkHref($row, setupUrl);
            }
        }else{
            setLinkText($row, "Upgrade");
            lockIcon($row);
            setLinkHref($row, upgradeUrl);
        }
    }

    function renderSearchSubmission(data){
        var pro = data['isPro'] === 1;
        var $row = jQuery("#search-submission");
        var num = data['sitemapUrlCount'];
        if(pro) {
            if (num === 0) {
                setLinkText($row, "Enabled");
            }
            else {
                setLinkText($row, num + " pages");
            }
            checkIcon($row);
            setLinkHref($row, profileUrl);
        }
        else {
            setLinkText($row, "Upgrade");
            lockIcon($row);
            setLinkHref($row, upgradeUrl);
        }
    }

    function renderDiagnostic(data){
        var issuesFound = data['diagnosticsFoundIssues'] === 1;
        var $row = jQuery("#diagnostics");
        var pro = data['isPro'] === 1;

        if(pro){
            if(issuesFound){
                setLinkText($row, "Issues found");
                warningIcon($row);
            }else{
                setLinkText($row, "No issues found");
                checkIcon($row);
            }
            setLinkHref($row, diagnosticsUrl);
        }else{
            setLinkText($row, "Upgrade");
            lockIcon($row);
            setLinkHref($row, upgradeUrl);
        }
    }

    function setLinkText($el, linkText){
        $el.find(".link").html(linkText);   
    }

    function setLinkHref($el, href){
        var link = "<a href=" + href + " target=\"_blank\" style=\"text-decoration:none\"></a>"
        $el.wrap(link);
    }

    function checkIcon($el){
        $el.find('.status-icon').html('<i class="fa fa-check-circle"></i>');
    }

    function timesIcon($el){
        $el.find('.status-icon').html('<i class="fa fa-times-circle"></i>');
    }

    function warningIcon($el){
        $el.find('.status-icon').html('<i class="fa fa-warning"></i>');
    }

    function spinIcon($el){
        $el.find('.status-icon').html('<i class="fa fa-circle-o-notch fa-spin"></i>');
    }

    function circleIcon($el){
        $el.find('.status-icon').html('<i class="fa fa-circle-thin"></i>');
    }

    function lockIcon($el){
        $el.find('.status-icon').html('<i class="fa fa-lock"></i>');
    }

    function refresh(){
        jQuery.getJSON(apiUrl,function(data) {
            var status = data['status'];
            if(status === 'none'){
                $activationSection.show();
                $dashboardSection.hide();
            }else{
                setTimeout(function(){
                    clearInterval(refreshInterval);
                    loadDashboard();
                }, 500);
            }
        });
    }

    function loadDashboard(){
        jQuery.getJSON(apiUrl,function(data) {
            renderSecurity(data);
            renderFloatingTrustmark(data);
            renderDirectoryListing(data);
            renderSearchHighlighting(data);
            renderEngagementTrustmark(data);
            renderSip(data);
            renderSearchSubmission(data);
            renderDiagnostic(data);

            if(data['isPro'] !== 1) {
                $sideframeSection.show();
                $proSection.show();
            }
            else if(data['tmEngagementInstalled'] !== 1) {
                $sideframeSection.show()
                $engagementSection.show();
            }
            else {
                $logoSection.show();
            }
            $dashboardSection.show();
        });
    }

    var refreshInterval = setInterval(refresh, 1000);
    refresh();
});
