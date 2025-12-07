const segment = '<div class="segment"><span></span><span></span><span></span></div>';

export const template = `<div class="metro-clock row">
                            <div class="metro-clock-shell">
                                <div class="metro-clock-shell-inner">
                                    <div class="metro-clock-shell-section">
                                        <div class="digit" data-type="days">${ segment.repeat(7) }</div>
                                        <div class="digit" data-type="days">${ segment.repeat(7) }</div>
                                    </div>
                                </div>
                            </div>
                            <div class="metro-clock-shell">
                                <div class="metro-clock-shell-inner">
                                    <div class="metro-clock-shell-section glass-flare" style="margin-right: 5px;padding-right:6px">
                                        <div class="digit" data-type="hours">${ segment.repeat(7) }</div>
                                        <div class="digit" data-type="hours">${ segment.repeat(7) }</div>
                                        <div class="spacer"></div>
                                        <div class="digit" data-type="minutes">${ segment.repeat(7) }</div>
                                        <div class="digit" data-type="minutes">${ segment.repeat(7) }</div>
                                    </div>
                                    <div class="metro-clock-shell-section glass-flare-reverse">
                                        <div class="digit" data-type="seconds">${ segment.repeat(7) }</div>
                                        <div class="digit" data-type="seconds">${ segment.repeat(7) }</div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
