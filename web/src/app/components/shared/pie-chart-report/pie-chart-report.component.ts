import { Component, ElementRef, HostListener, Input, OnChanges, ViewChild } from '@angular/core';
import * as d3 from 'd3';
import { PieChartReportType } from '../../../interfaces/pie-chart-report-type';
import { TYPES } from '../../../constants/types.constant';

let element;
let arc;
let outerArc;
let color;
let pie;
let slice;
let legend;
let legendData;
let svg;
let key;
let text;
let polyline;
let width: number;
let height: number;
let radius: number;
let margin: {top: number, right: number, bottom: number, left: number};
const DIVIDER = 16;
@Component({
  selector: 'app-pie-chart-report',
  templateUrl: './pie-chart-report.component.html',
  styleUrls: ['./pie-chart-report.component.scss']
})
export class PieChartReportComponent implements OnChanges {
    // @ts-ignore
    @ViewChild('report_container') reportContainer: ElementRef;
    @Input() data: PieChartReportType[];

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
        width = element.clientWidth - margin.left;
        height = (element.clientWidth / 2) - margin.top - margin.bottom;
        radius = Math.min(width, height) / 2;

        d3.select('svg.pie-chart').remove();

        svg = d3.select(element).append('svg').classed('pie-chart', true)
            .attr('width', width).attr('height', height)
            .append('g').attr('transform', 'translate(' + width / 2 + ',' + height / 2 + ')');

        svg.append('g').attr('class', 'slices');
        svg.append('g').attr('class', 'labels');
        svg.append('g').attr('class', 'lines');
        svg.append('g').attr('class', 'legend');

        pie = d3.pie()
            .sort(null)
            .value((d) => d.amount);

        arc = d3.arc()
            .outerRadius(radius * 0.9)
            .innerRadius(radius * 0.5);

        outerArc = d3.arc()
            .innerRadius(radius * 0.9)
            .outerRadius(radius * 0.9);

        key = (d) => { return d.data.label; };

        color = {'tournament': 'green', "deposit": 'red'};
        legendData = [{label: '', amount: '', currency: ''}];
    }

    private draw(data) {
        this.drawSlices(data);
        this.drawLabels(data);
        this.drawLines(data);
        this.drawLegend(legendData);
    }

    private drawSlices(data) {
        slice = svg.select('.slices').selectAll('path.slice').data(pie(data), key);

        slice.exit().remove();

        slice.enter()
            .append('path')
            .style('fill', (d) => color[d.data.label])
            .attr('slice', key)
            .attr('d', arc)
            .on('mouseenter', this.mouseenter)
            .on('mouseout', this.mouseout);
    }

    private drawLabels(data) {
        text = svg.select('.labels').selectAll('text').data(pie(data), key);

        text.exit().remove();

        text.enter()
            .append('text')
            .attr('dy', '.35em')
            .attr('transform', (d) => {
                const pos = outerArc.centroid(d);
                pos[0] = radius * (this.midAngle(d) < Math.PI ? 1 : -1);
                return 'translate(' + pos + ')'
            })
            .attr('text-anchor', (d) => {
                return this.midAngle(d) < Math.PI ? 'start': 'end';
            })
            .text((d) => TYPES.FINANCE_TYPE[d.data.label.toUpperCase()] + ' (' + d.data.amount + d.data.currency +  ')');
    }

    private drawLines(data) {
        polyline = svg.select('.lines').selectAll('polyline').data(pie(data), key);

        polyline.exit().remove();

        polyline.enter()
            .append('polyline')
            .attr('points', (d) => {
                const pos = outerArc.centroid(d);
                pos[0] = radius * 0.95 * (this.midAngle(d) < Math.PI ? 1 : -1);
                return [arc.centroid(d), outerArc.centroid(d), pos];
            });
    }

    private drawLegend(data) {
        legend = svg.select('.legend').selectAll('text').data(data);

        legend.exit().remove();

        legend.enter()
            .append('text')
            .attr('class', 'title')
            .attr('dy', -8)
            .text((d) => d.label);
        legend.enter()
            .append('text')
            .attr('class', 'value')
            .attr('dy', 16)
            .text((d) => d.amount + d.currency);
    }

    private midAngle(d) {
        return d.startAngle + (d.endAngle - d.startAngle) / 2;
    }

    private mouseenter(d) {
        svg.select('path[slice="' + key(d) + '"]').classed('focus', true);
        d3.select('.legend .title').text(() => TYPES.FINANCE_TYPE[d.data.label.toUpperCase()]);
        d3.select('.legend .value').text(() => d.data.amount + d.data.currency);
    }

    private mouseout(d) {
        svg.select('path[slice="' + key(d) + '"]').classed('focus', false);
        d3.select('.legend .title').text(() => legendData[0].label);
        d3.select('.legend .value').text(() => legendData[0].amount + legendData[0].currency);
    }

}
