// Initialize DataTables
if (typeof $.fn.DataTable !== 'undefined') {
    $('#bookingsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
        },
        order: [
            [0, 'desc']
        ],
        responsive: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Tat ca"]
        ]
    });

    $('#roomUsageTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
        },
        order: [
            [3, 'desc']
        ],
        responsive: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Tat ca"]
        ]
    });

    $('#userActivityTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
        },
        order: [
            [2, 'desc']
        ],
        responsive: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Tat ca"]
        ]
    });

    $('#maintenanceTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/vi.json'
        },
        order: [
            [0, 'desc']
        ],
        responsive: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Tat ca"]
        ]
    });
}

// Toggle custom date range based on selection
$('#time_range').change(function() {
    if ($(this).val() === 'custom') {
        $('#date_range_container').show();
    } else {
        $('#date_range_container').hide();
    }
});

// Reset filter
$('#resetFilter').click(function() {
    $('#report_type').val('all');
    $('#time_range').val('month');
    $('#date_range_container').hide();
    $('#start_date').val(startDate);
    $('#end_date').val(endDate);
});

// Initialize booking trends chart with enhanced styling
function initBookingChart(labels, data) {
    var bookingCtx = document.getElementById('bookingTrendsChart');
    if (bookingCtx) {
        // Create gradient fill
        var ctx = bookingCtx.getContext('2d');
        var gradientFill = ctx.createLinearGradient(0, 0, 0, 350);
        gradientFill.addColorStop(0, "rgba(78, 115, 223, 0.3)");
        gradientFill.addColorStop(1, "rgba(78, 115, 223, 0.0)");

        var myLineChart = new Chart(bookingCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: "Dat phong",
                    lineTension: 0.3,
                    backgroundColor: gradientFill,
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 4,
                    pointBackgroundColor: "#fff",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "#fff",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: data,
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        time: {
                            unit: 'date'
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7,
                            color: "#77838f",
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            beginAtZero: true,
                            color: "#77838f",
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return value;
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244, 0.7)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFont: {
                            size: 14
                        },
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                return label + ': ' + context.parsed.y + ' luot';
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                },
                hover: {
                    mode: 'nearest',
                    intersect: false
                }
            }
        });
    }
}

// Initialize room usage pie chart with enhanced styling
function initRoomUsageChart(labels, data) {
    var roomUsageCtx = document.getElementById('roomUsageChart');
    if (roomUsageCtx) {
        var myPieChart = new Chart(roomUsageCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.9)',
                        'rgba(28, 200, 138, 0.9)',
                        'rgba(54, 185, 204, 0.9)'
                    ],
                    hoverBackgroundColor: [
                        'rgba(46, 89, 217, 1)',
                        'rgba(23, 166, 115, 1)',
                        'rgba(44, 159, 175, 1)'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                var dataset = context.dataset;
                                var currentValue = dataset.data[context.dataIndex];
                                var total = dataset.data.reduce(function(previousValue, currentValue) {
                                    return previousValue + currentValue;
                                });
                                var percentage = Math.round((currentValue / total) * 100);
                                return context.chart.data.labels[context.dataIndex] + ': ' + percentage + '%';
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            color: '#858796',
                            boxWidth: 12,
                            padding: 15
                        }
                    },
                },
                cutout: '70%',
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1500,
                    easing: 'easeInOutQuart'
                },
                elements: {
                    arc: {
                        borderWidth: 2
                    }
                }
            }
        });
    }
}

// Toggle sections based on report type selection
$('#report_type').change(function() {
    const reportType = $(this).val();
    if (reportType === 'all') {
        $('#bookingsReportSection, #roomUsageReportSection, #userActivityReportSection, #maintenanceReportSection').show();
    } else {
        $('#bookingsReportSection, #roomUsageReportSection, #userActivityReportSection, #maintenanceReportSection').hide();
        $(`#${reportType}ReportSection`).show();
    }
});

// Print report
$('#printReport').click(function() {
    window.print();
});

// Export report
$('#exportReport').click(function() {
    alert('Chuc nang xuat bao cao dang duoc phat trien');
});
