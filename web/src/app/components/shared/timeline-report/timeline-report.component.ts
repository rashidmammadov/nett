import { Component, ElementRef, HostListener, Input, OnChanges, ViewChild } from '@angular/core';
import * as d3 from 'd3';
import { TimelineReportType } from '../../../interfaces/timeline-report-type';

let element;
let margin: {top: number, right: number, bottom: number, left: number};
let width: number;
let height: number;
let svg;
let xAxis;
let yAxis;
let lineChart;
let tooltip;
const DIVIDER = 16;
@Component({
  selector: 'app-timeline-report',
  templateUrl: './timeline-report.component.html',
  styleUrls: ['./timeline-report.component.scss']
})
export class TimelineReportComponent implements OnChanges {
    // @ts-ignore
    @ViewChild('report_container') reportContainer: ElementRef;
    @Input() data: TimelineReportType[];

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

        xAxis = d3.scaleTime().rangeRound([DIVIDER / 2, width - margin.left - margin.right - DIVIDER / 2]);
        yAxis = d3.scaleLinear().range([height - margin.top - margin.bottom, 0]);

        lineChart = d3.line()
            .x((d: TimelineReportType) => xAxis(d.startDate))
            .y((d: TimelineReportType) => yAxis(d.tournamentRanking));

        d3.select('svg.timeline').remove();

        svg = d3.select(element).append('svg').classed('timeline', true)
            .attr('width', width).attr('height', height)
            .append('g').attr('transform', 'translate(' + margin.left * 2 + ',' + margin.top + ')');

        d3.select('app-timeline-report .directive-tooltip').remove();
        tooltip = d3.select(element).append('div')
            .attr('class', 'directive-tooltip')
            .style('display', 'none');
    }

    private draw(data) {
      this.drawXAxis(data);
      this.drawYAxis(data);
      this.drawBarChart(data);
      this.drawLineChart(data);
      this.drawCircles(data)
    }

    drawXAxis(data) {
        xAxis.domain(d3.extent(data, (d: TimelineReportType) => d.startDate));
        svg.append('g').attr('class', 'x-axis').call(d3.axisTop(xAxis));
    }

    drawYAxis(data) {
        yAxis.domain([d3.max(data, (d: TimelineReportType) => d.participantCount), 1]);
        svg.append('g').attr('class', 'y-axis')
            .call(d3.axisLeft(yAxis).tickSize(-(width - margin.left - margin.right)));
    }

    private drawBarChart(data) {
        svg.append('g').selectAll('bar')
            .data(data).enter()
            .append('rect')
                .attr('class', 'bar')
                .attr('tournament-id', (d: TimelineReportType) => d.tournamentId)
                .attr('x', (d: TimelineReportType) => xAxis(d.startDate) - DIVIDER / 2)
                .attr('width', DIVIDER)
                .transition().duration(300)
                .attr('height', (d: TimelineReportType) => yAxis(d.participantCount));
    }

    private drawLineChart(data) {
        svg.append('path')
            .data([data])
            .attr('class', 'line')
            .attr('d', lineChart);
    }

    private drawCircles(data) {
        svg.append('g').selectAll('circle')
            .data(data).enter()
            .append('circle')
                .attr('class', 'circle')
                .attr('tournament-id', (d) => d.tournamentId)
                .attr('cx', (d: TimelineReportType) => xAxis(d.startDate))
                .attr('cy', (d: TimelineReportType) => yAxis(d.tournamentRanking))
                .on('mouseover', this.mouseover)
                .on('mousemove', this.mouseover)
                .on('mouseout', this.mouseout)
                .attr('r', DIVIDER / 3);
    }

    mouseover(d: TimelineReportType) {
        d3.select('app-timeline-report .circle[tournament-id="' + d.tournamentId + '"]')
            .transition().duration(300).attr('r', DIVIDER / 2);
        d3.selectAll('app-timeline-report .bar').attr('opacity', 0.5);
        d3.select('app-timeline-report .bar[tournament-id="' + d.tournamentId + '"]').attr('opacity', 1);

        const event = d3.event;
        tooltip.style('left', event.offsetX + margin.left + margin.right + 'px');
        tooltip.style('top', event.offsetY - margin.top - margin.bottom + 'px');

        tooltip.style('display', 'inline-block');
        tooltip.html(`<img src="${d.gameImage}"><br/>
            <b>${d.gameName}</b><br/>
            Sıralama: <b>${d.tournamentRanking}. yer</b><br/>
            Katılımcı Sayısı: <b>${d.participantCount} kişi</b><br/>
            Turnuva Tarihi: <b>${new Date(d.startDate).getDate() + '/' + (new Date(d.startDate).getMonth() + 1) + '/' + 
              new Date(d.startDate).getFullYear()}</b>`);
    }

    mouseout() {
        d3.selectAll('app-timeline-report circle').attr('r', DIVIDER / 3);
        d3.selectAll('app-timeline-report .bar').attr('opacity', 1);
        tooltip.style('display', 'none');
    }

}
