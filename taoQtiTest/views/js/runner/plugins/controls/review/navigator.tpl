<div class="qti-panel qti-navigator{{#if hidden}} hidden{{/if}}">
    <div class="qti-navigator-collapsible">
        <span class="qti-navigator-collapse icon icon-left" title="{{__ 'Collapse the review panel'}}"></span>
        <span class="qti-navigator-expand icon icon-right" title="{{__ 'Expand the review panel'}}"></span>
    </div>

    <div class="qti-navigator-info collapsible">
                <span class="qti-navigator-label">
                    <span class="qti-navigator-text">{{__ 'Test status'}}</span>
                    <span class="icon-up"></span>
                    <span class="icon-down"></span>
                </span>
        <ul class="collapsible-panel plain">
            <li class="qti-navigator-viewed" title="{{__ 'Viewed items'}}">
                        <span class="qti-navigator-label">
                            <span class="qti-navigator-icon icon-viewed"></span>
                            <span class="qti-navigator-text">{{__ 'Viewed'}}</span>
                            <span class="qti-navigator-counter">-/-</span>
                        </span>
            </li>
            <li class="qti-navigator-answered" title="{{__ 'Completed items'}}">
                        <span class="qti-navigator-label">
                            <span class="qti-navigator-icon icon-answered"></span>
                            <span class="qti-navigator-text">{{__ 'Answered'}}</span>
                            <span class="qti-navigator-counter">-/-</span>
                        </span>
            </li>
            <li class="qti-navigator-unanswered" title="{{__ 'Unanswered items'}}">
                        <span class="qti-navigator-label">
                            <span class="qti-navigator-icon icon-unanswered"></span>
                            <span class="qti-navigator-text">{{__ 'Unanswered'}}</span>
                            <span class="qti-navigator-counter">-/-</span>
                        </span>
            </li>
            <li class="qti-navigator-flagged" title="{{__ 'Items marked for later review'}}">
                        <span class="qti-navigator-label">
                            <span class="qti-navigator-icon icon-flagged"></span>
                            <span class="qti-navigator-text">{{__ 'Flagged'}}</span>
                            <span class="qti-navigator-counter">-/-</span>
                        </span>
            </li>
        </ul>
    </div>

    <div class="qti-navigator-filters">
        <ul class="plain clearfix">
            <li class="qti-navigator-filter active" data-mode="all">
                <span title="{{__ 'Reset filters'}}">{{__ 'All'}}</span>
            </li>
            <li class="qti-navigator-filter" data-mode="unanswered">
                <span class="icon-unanswered" title="{{__ 'Only display the unanswered items'}}"></span>
            </li>
            <li class="qti-navigator-filter" data-mode="flagged">
                <span class="icon-flagged" title="{{__ 'Only display the items marked for review'}}"></span>
            </li>
        </ul>
    </div>

    <nav class="qti-navigator-tree"></nav>

    <div id="qti-navigator-linear" class="qti-navigator-linear">
        <span class="icon icon-info" title="{{__ 'In this part of the test navigation is not allowed.'}}"></span>
        <p class="qti-navigator-message">
            {{__ 'In this part of the test navigation is not allowed.'}}
        </p>
    </div>
</div>
