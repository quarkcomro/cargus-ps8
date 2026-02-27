<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Cargus V3 Premium Configuration' mod='cargus'}
    </div>

    <div class="alert alert-info">
        <p><strong>{l s='How to use this module:' mod='cargus'}</strong></p>
        <ul>
            <li>{l s='Step 1: Enter your Cargus API V3 credentials in the "Account & API" tab and save.' mod='cargus'}</li>
            <li>{l s='Step 2: Go to the "Preferences & Services" tab to set your default pickup location and heavy cargo thresholds.' mod='cargus'}</li>
            <li>{l s='Step 3: Use the "API Debugger" tab to test your connection without placing a real order.' mod='cargus'}</li>
        </ul>
    </div>

    <ul class="nav nav-tabs" id="cargusConfigTabs" role="tablist">
        <li class="active"><a href="#tab-api" role="tab" data-toggle="tab">1. {l s='ACCOUNT & API' mod='cargus'}</a></li>
        <li><a href="#tab-preferences" role="tab" data-toggle="tab">2. {l s='PREFERENCES & SERVICES' mod='cargus'}</a></li>
        <li><a href="#tab-debugger" role="tab" data-toggle="tab">3. {l s='API DEBUGGER' mod='cargus'}</a></li>
    </ul>

    <div class="tab-content" style="margin-top: 20px;">
        
        <div class="tab-pane active" id="tab-api">
            <form action="" method="post" class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-lg-3 required">{l s='API URL' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_API_URL" value="{$cargus_api_url|escape:'htmlall':'UTF-8'}" placeholder="https://urgentcargus.azure-api.net/api/" class="form-control" required />
                        <p class="help-block">{l s='Must end with a slash (/).' mod='cargus'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3 required">{l s='Subscription Key' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_SUBSCRIPTION_KEY" value="{$cargus_subscription_key|escape:'htmlall':'UTF-8'}" placeholder="e.g., d991c90..." class="form-control" required />
                        <p class="help-block">{l s='Your unique V3 API key provided by Cargus.' mod='cargus'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3 required">{l s='Username' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_USERNAME" value="{$cargus_username|escape:'htmlall':'UTF-8'}" class="form-control" required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3 required">{l s='Password' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="password" name="CARGUS_PASSWORD" value="{$cargus_password|escape:'htmlall':'UTF-8'}" class="form-control" required />
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" value="1" id="module_form_submit_btn" name="submitCargusConfig" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Save Configuration' mod='cargus'}
                    </button>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="tab-preferences">
            <form action="" method="post" class="form-horizontal">
                
                {if $api_error}
                    <div class="alert alert-warning">
                        {l s='Cannot load pickup locations. Please ensure API credentials in Tab 1 are correct.' mod='cargus'}<br>
                        <strong>{l s='Error:' mod='cargus'}</strong> {$api_error|escape:'htmlall':'UTF-8'}
                    </div>
                {/if}

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Default Pickup Location' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_PICKUP_LOCATION" class="form-control">
                            <option value="">-- {l s='Select Location' mod='cargus'} --</option>
                            {foreach from=$pickupLocations item=location}
                                <option value="{$location.LocationId|escape:'htmlall':'UTF-8'}" {if $cargus_pickup_location == $location.LocationId}selected{/if}>
                                    {$location.Name|escape:'htmlall':'UTF-8'} ({$location.LocalityName|escape:'htmlall':'UTF-8'})
                                </option>
                            {/foreach}
                        </select>
                        <p class="help-block">{l s='Populated automatically from /PickupLocations endpoint.' mod='cargus'}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Default Package Type' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_PACKAGE_TYPE" class="form-control">
                            <option value="Envelope" {if $cargus_package_type == 'Envelope'}selected{/if}>{l s='Envelope (Plic)' mod='cargus'}</option>
                            <option value="Parcel" {if $cargus_package_type == 'Parcel'}selected{/if}>{l s='Parcel (Colet)' mod='cargus'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Shipping Payer' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_PAYER" class="form-control">
                            <option value="Sender" {if $cargus_payer == 'Sender'}selected{/if}>{l s='Sender (Expeditor)' mod='cargus'}</option>
                            <option value="Recipient" {if $cargus_payer == 'Recipient'}selected{/if}>{l s='Recipient (Destinatar)' mod='cargus'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Heavy Cargo Threshold (KG)' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="number" step="0.1" name="CARGUS_HEAVY_THRESHOLD" value="{$cargus_heavy_threshold|escape:'htmlall':'UTF-8'}" class="form-control" />
                        <p class="help-block">{l s='Orders exceeding this weight will trigger the Agabaritic (Heavy Cargo) smart split logic.' mod='cargus'}</p>
                    </div>
                </div>

                <div class="panel-footer">
                    <button type="submit" value="1" name="submitCargusConfig" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Save Configuration' mod='cargus'}
                    </button>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="tab-debugger">
            <div class="row">
                <div class="col-lg-12">
                    <button class="btn btn-primary" id="btn-test-locations" style="margin-right: 10px;">
                        <i class="icon-map-marker"></i> {l s='Test Locații' mod='cargus'}
                    </button>
                    <button class="btn btn-default" id="btn-test-tarife" style="margin-right: 10px;">
                        <i class="icon-money"></i> {l s='Test Tarife' mod='cargus'}
                    </button>
                    <button class="btn btn-default" id="btn-test-servicii">
                        <i class="icon-cogs"></i> {l s='Test Servicii' mod='cargus'}
                    </button>
                </div>
            </div>
            
            <div class="row" style="margin-top: 20px;">
                <div class="col-lg-12">
                    <div id="cargus-console" class="api-tester-console-output" style="background: #1e1e1e; color: #4caf50; padding: 15px; border-radius: 5px; height: 300px; overflow-y: auto; font-family: monospace;">
                        {l s='System ready for testing...' mod='cargus'}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    var consoleDiv = document.getElementById('cargus-console');
    
    function appendLog(message, isError) {
        var p = document.createElement('div');
        p.style.color = isError ? '#ff4c4c' : '#4caf50';
        p.style.marginBottom = '5px';
        p.innerText = '[' + new Date().toLocaleTimeString() + '] > ' + message;
        consoleDiv.appendChild(p);
        consoleDiv.scrollTop = consoleDiv.scrollHeight;
    }

    function runAjaxTest(actionName, btnId, loadingText) {
        var btn = document.getElementById(btnId);
        if(!btn) return;
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            appendLog(loadingText, false);
            
            // Path directly to the ajax.php file we created earlier
            var targetUrl = '../modules/cargus/ajax.php?action=' + actionName;
            
            fetch(targetUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                appendLog(data.message, !data.success);
            })
            .catch(function(error) {
                appendLog('Network error: ' + error.message, true);
            });
        });
    }

    runAjaxTest('TestLocations', 'btn-test-locations', 'Requesting locations endpoint...');
    runAjaxTest('TestTarife', 'btn-test-tarife', 'Requesting pricing endpoint...');
    runAjaxTest('TestServicii', 'btn-test-servicii', 'Requesting services endpoint...');
});
</script>
