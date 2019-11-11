import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import * as d3 from 'd3';

@Component({
  selector: 'app-timeline-report',
  templateUrl: './timeline-report.component.html',
  styleUrls: ['./timeline-report.component.scss']
})
export class TimelineReportComponent implements OnInit {
    // @ts-ignore
    @ViewChild('report_container') reportContainer: ElementRef;
    element;
    margin: {top: number, right: number, bottom: number, left: number};
    width: number;
    height: number;
    svg;
    timeParser;
    xAxis;
    yAxis;
    lineChart;
    protected static DIVIDER = 16;
    protected static MAX_PARTICIPANTS: number = 32;

    constructor() { }

    ngOnInit() {
        this.prepare();
        const data = [{
            date: new Date(2019, 8, 6),
            participants: 24,
            ranking: 15
        }, {
            date: new Date(2019, 9, 6),
            participants: 32,
            ranking: 25
        }, {
            date: new Date(2019, 10, 6),
            participants: 16,
            ranking: 1
        }];
        this.draw(data);
    }

    private prepare() {
        this.element = this.reportContainer.nativeElement;
        this.margin = {
            top: TimelineReportComponent.DIVIDER,
            right: TimelineReportComponent.DIVIDER,
            bottom: TimelineReportComponent.DIVIDER,
            left: TimelineReportComponent.DIVIDER
        };
        this.width = this.element.clientWidth - this.margin.left;
        this.height = (this.element.clientWidth / 2) - this.margin.top - this.margin.bottom;
        this.timeParser = d3.timeParse("%d-%b-%y");

        this.xAxis = d3.scaleTime().rangeRound([
            TimelineReportComponent.DIVIDER / 2,
            this.width - this.margin.left - this.margin.right - TimelineReportComponent.DIVIDER / 2
        ]);
        this.yAxis = d3.scaleLinear().range([this.height - this.margin.top - this.margin.bottom, 0]);

        this.lineChart = d3.line()
            .x((d) => this.xAxis(d.date))
            .y((d) => this.yAxis(d.ranking));

        d3.select('svg.timeline').remove();

        this.svg = d3.select(this.element).append('svg').classed('timeline', true)
            .attr('width', this.width).attr('height', this.height)
            .append('g').attr('transform', 'translate(' + this.margin.left * 2 + ',' + this.margin.top + ')');
    }

    private draw(data) {
        this.xAxis.domain(d3.extent(data, (d) => d.date));
        this.yAxis.domain([TimelineReportComponent.MAX_PARTICIPANTS, 1]);

        this.svg.append('g')
            .call(d3.axisTop(this.xAxis));

        this.svg.append('g')
            .call(d3.axisLeft(this.yAxis));

        this.svg.append('g').selectAll('bar')
            .data(data)
            .enter().append('rect')
                .attr('class', 'bar')
                .attr('x', (d) => this.xAxis(d.date) - TimelineReportComponent.DIVIDER / 2)
                .attr('width', TimelineReportComponent.DIVIDER)
                .transition().duration(300)
                .attr('height', (d) => this.yAxis(d.participants));

        this.svg.append('path')
            .data([data])
            .attr('class', 'line')
            .attr('d', this.lineChart);
    }

}
