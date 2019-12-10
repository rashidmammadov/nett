import { Component, ElementRef, HostListener, Input, OnChanges, ViewChild } from '@angular/core';
import * as d3 from 'd3';
import { HalfDonutReportType } from '../../../interfaces/half-donut-report-type';
import {VisualService} from "../../../services/visual/visual.service";

let element;
let svg;
let pie;
let arc;
let width;
let height;
let angleStart;
let anglesRange;
let radius;
let colors;
let margin: {top: number, right: number, bottom: number, left: number};
let difference;
let dataCount;
let tooltip;
const DIVIDER = 16;
@Component({
  selector: 'app-half-donut-report',
  templateUrl: './half-donut-report.component.html',
  styleUrls: ['./half-donut-report.component.scss']
})
export class HalfDonutReportComponent implements OnChanges {
    @ViewChild('report_container', {static: true}) reportContainer: ElementRef;
    @Input() data: HalfDonutReportType;

    constructor() { }

    @HostListener('window:resize', ['$event'])
    onResize() {
        this.prepare();
        this.draw(this.data);
    }

    ngOnChanges() {
        if (!this.data) { return; }
        this.prepare();
        this.draw(this.data);
    }

    private prepare() {
        element = this.reportContainer.nativeElement;
        margin = {
            top: DIVIDER,
            right: DIVIDER,
            bottom: DIVIDER,
            left: DIVIDER
        };

        width = element.clientWidth;
        height = width / 2 + margin.top + margin.bottom;
        angleStart = -(0.50 * Math.PI);
        anglesRange = 0.50 * Math.PI;
        radius = Math.min(width, height) / 2;
        colors = ['#7B1FA2', 'transparent'];
        dataCount = 0;

        pie = d3.pie()
            .sort(null)
            .value(d => d.earningPercentage)
            .startAngle(angleStart);

        arc = d3.arc();

        d3.select('svg.half-donut').remove();

        let translation = (x, y) => `translate(${x}, ${y})`;

        svg = d3.select(element).append('svg').classed('half-donut', true)
            .attr('width', width).attr('height', height / 2 + margin.top + margin.bottom)
            .append('g').attr('transform', translation(width / 2 + margin.left, height / 2 + margin.top));

        d3.select('app-half-donut-report .directive-tooltip').remove();
        tooltip = d3.select(element).append('div')
            .attr('class', 'directive-tooltip')
            .style('display', 'none');
    }

    private draw(data) {
        difference = (radius - DIVIDER * 2) / data.length;
        this.drawPies(data);
    }

    private drawPies(data) {
        const layers = svg.selectAll('g').data(data).enter().append('g').classed('slice', true);
        const pies = layers.selectAll('path')
            .data((d) => pie.endAngle(this.prepareEndAngle(d.earningPercentage))([d]));
        this.drawArcs(pies);
    }

    private drawArcs(pies) {
        pies.enter().append('path')
            .attr('pie', (d) => `tournament-${d.data.tournamentId}`)
            .attr('stroke', (d, i) => !i ? '#F5F5F5' : 'transparent')
            .attr('stroke-width', '1.5px')
            .attr('fill', (d, i) => colors[i])
            .attr('d', this.prepareArcSlice)
            .on('mouseover', this.mouseover)
            .on('mousemove', this.mouseover)
            .on('mouseout', this.mouseout);
    }

    private prepareArcSlice(d: HalfDonutReportType, i) {
        if (i === 0 && dataCount > 0) {
            radius = radius - difference;
        }
        dataCount++;
        return arc.outerRadius(radius)
            .innerRadius(radius - difference)
            .cornerRadius(DIVIDER / 2)(d);
    }

    private prepareEndAngle(value) {
        return (value * anglesRange) / 100;
    }

    mouseover(d) {
        svg.select('path[pie="tournament-' + (d.data.tournamentId) + '"]').classed('focus', true);
        const event = d3.event;
        tooltip.style('left', event.offsetX + (((width - 190) <= event.offsetX) ?
          - (160 + margin.left + margin.right) : (margin.left + margin.right)) + 'px');
        tooltip.style('top', event.offsetY - margin.top - margin.bottom + 'px');

        tooltip.style('display', 'inline-block');
        tooltip.html(VisualService.prepareTooltipHTML(d.data));
    }

    mouseout(d, i) {
        svg.select('path[pie="tournament-' + (d.data.tournamentId) + '"]').classed('focus', false);
        tooltip.style('display', 'none');
    }

}
