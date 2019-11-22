export interface MostPlayedReportType {
    activeStatusCount: number | null;
    cancelStatusCount: number | null;
    closeStatusCount: number | null;
    openStatusCount:  number | null;
    totalCount:  number | null;
    gameId: number;
    gameImage: string;
    gameName: string;
}
