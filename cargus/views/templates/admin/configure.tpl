<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Cargus V3 Premium Configuration' mod='cargus'}
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
                        <input type="text" name="CARGUS_API_URL" value="{$cargus_api_url|escape:'htmlall':'UTF-8'}" class="form-control" required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3 required">{l s='Subscription Key' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_SUBSCRIPTION_KEY" value="{$cargus_subscription_key|escape:'htmlall':'UTF-8'}" class="form-control" required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3 required">{l s='User WebExpress' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_USERNAME" value="{$cargus_username|escape:'htmlall':'UTF-8'}" class="form-control" required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3 required">{l s='Parolă' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="password" name="CARGUS_PASSWORD" value="{$cargus_password|escape:'htmlall':'UTF-8'}" class="form-control" required />
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" value="1" name="submitCargusConfig" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Salvează Configurarea Modulului' mod='cargus'}
                    </button>
                </div>
            </form>
        </div>

        <div class="tab-pane" id="tab-preferences">
            <form action="" method="post" class="form-horizontal">
                
                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Punct Ridicare Implicit' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_PICKUP_LOCATION" class="form-control">
                            <option value="">-- {l s='Select Location' mod='cargus'} --</option>
                            {if $pickup_locations}
                                {foreach from=$pickup_locations item=location}
                                    <option value="{$location.LocationId|escape:'htmlall':'UTF-8'}" {if $cargus_pickup_location == $location.LocationId}selected{/if}>
                                        {$location.Name|escape:'htmlall':'UTF-8'} ({$location.LocalityName|escape:'htmlall':'UTF-8'})
                                    </option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Plan Tarifar' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_PRICE_PLAN" class="form-control">
                            <option value="{$cargus_price_plan|escape:'htmlall':'UTF-8'}">{$cargus_price_plan|escape:'htmlall':'UTF-8'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Serviciu Implicit' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_DEFAULT_SERVICE" class="form-control">
                            <option value="{$cargus_default_service|escape:'htmlall':'UTF-8'}">{$cargus_default_service|escape:'htmlall':'UTF-8'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Plătitor Expediție' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_PAYER" class="form-control">
                            <option value="Expeditor" {if $cargus_payer == 'Expeditor'}selected{/if}>{l s='Expeditor' mod='cargus'}</option>
                            <option value="Destinatar" {if $cargus_payer == 'Destinatar'}selected{/if}>{l s='Destinatar' mod='cargus'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Tip Ramburs' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_COD_TYPE" class="form-control">
                            <option value="Numerar" {if $cargus_cod_type == 'Numerar'}selected{/if}>{l s='Numerar' mod='cargus'}</option>
                            <option value="Cont Colector" {if $cargus_cod_type == 'Cont Colector'}selected{/if}>{l s='Cont Colector' mod='cargus'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Tip Expediție' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <select name="CARGUS_SHIPMENT_TYPE" class="form-control">
                            <option value="Plic" {if $cargus_shipment_type == 'Plic'}selected{/if}>{l s='Plic' mod='cargus'}</option>
                            <option value="Colet" {if $cargus_shipment_type == 'Colet'}selected{/if}>{l s='Colet' mod='cargus'}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Deschidere Colet' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="CARGUS_OPEN_PACKAGE" id="CARGUS_OPEN_PACKAGE_on" value="1" {if $cargus_open_package == 1}checked="checked"{/if}>
                            <label for="CARGUS_OPEN_PACKAGE_on">Yes</label>
                            <input type="radio" name="CARGUS_OPEN_PACKAGE" id="CARGUS_OPEN_PACKAGE_off" value="0" {if $cargus_open_package == 0}checked="checked"{/if}>
                            <label for="CARGUS_OPEN_PACKAGE_off">No</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Livrare Sâmbăta' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="CARGUS_SATURDAY_DELIVERY" id="CARGUS_SATURDAY_DELIVERY_on" value="1" {if $cargus_saturday_delivery == 1}checked="checked"{/if}>
                            <label for="CARGUS_SATURDAY_DELIVERY_on">Yes</label>
                            <input type="radio" name="CARGUS_SATURDAY_DELIVERY" id="CARGUS_SATURDAY_DELIVERY_off" value="0" {if $cargus_saturday_delivery == 0}checked="checked"{/if}>
                            <label for="CARGUS_SATURDAY_DELIVERY_off">No</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Asigurare (Valoare Declarată)' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="CARGUS_INSURANCE" id="CARGUS_INSURANCE_on" value="1" {if $cargus_insurance == 1}checked="checked"{/if}>
                            <label for="CARGUS_INSURANCE_on">Yes</label>
                            <input type="radio" name="CARGUS_INSURANCE" id="CARGUS_INSURANCE_off" value="0" {if $cargus_insurance == 0}checked="checked"{/if}>
                            <label for="CARGUS_INSURANCE_off">No</label>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Preț Bazic Standard' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_BASIC_PRICE_STD" value="{$cargus_basic_price_std|escape:'htmlall':'UTF-8'}" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Preț Bazic PUDO' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_BASIC_PRICE_PUDO" value="{$cargus_basic_price_pudo|escape:'htmlall':'UTF-8'}" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Preț KG Extra' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_EXTRA_KG_PRICE" value="{$cargus_extra_kg_price|escape:'htmlall':'UTF-8'}" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Taxă Ramburs (COD Fee)' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="text" name="CARGUS_COD_FEE" value="{$cargus_cod_fee|escape:'htmlall':'UTF-8'}" class="form-control" />
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Prag Greutate Agabaritic (KG)' mod='cargus'}</label>
                    <div class="col-lg-6">
                        <input type="number" step="0.1" name="CARGUS_HEAVY_THRESHOLD" value="{$cargus_heavy_threshold|escape:'htmlall':'UTF-8'}" class="form-control" />
                        <p class="help-block">{l s='Acesta declanșează logica "Smart Split" pentru Heavy Cargo.' mod='cargus'}</p>
                    </div>
                </div>

                <div class="panel-footer">
                    <button type="submit" value="1" name="submitCargusConfig" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Salvează Configurarea Modulului' mod='cargus'}
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
                        System ready for testing...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    var consoleDiv = document.getElementById('cargus-console');
    var baseAjaxUrl = '{$cargus_ajax_link|escape:"javascript":"UTF-8"}';

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
            
            var targetUrl = baseAjaxUrl + '&ajax=1&action=' + actionName;
            
            fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                appendLog(data.message, !data.success);
            })
            .catch(function(error) {
                appendLog('Eroare Rețea/Server: ' + error.message, true);
            });
        });
    }

    runAjaxTest('TestLocations', 'btn-test-locations', 'Se testează comunicarea cu API-ul...');
    runAjaxTest('TestTarife', 'btn-test-tarife', 'Se testează punctul de calcul...');
    runAjaxTest('TestServicii', 'btn-test-servicii', 'Se testează punctul de servicii...');
});
</script>
