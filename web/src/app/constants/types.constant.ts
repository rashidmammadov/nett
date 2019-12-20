export const TYPES = {
    FINANCE_STATUS: {
        0: 'Onaylandı',
        1: 'Onay Bekliyor',
        2: 'İşlem Emri Verildi',
        99: 'İptal Edildi',
    },

    FINANCE_TYPE: {
        'deposit': 'Para Yatırma',
        'tournament': 'Kazanç',
        'withdraw': 'Para Çekme'
    },

    MIN_COEFFICIENT: 5.5,

    MIN_PARTICIPANT_COUNT: 16,

    MAX_PARTICIPANT_COUNT: 32,

    MIN_PARTICIPATION_FEE: 15,

    MAX_PARTICIPATION_FEE: 20,

    PAYMENT: {
        'MONEY': 'money',
        'TICKET': 'ticket'
    },

    REPORT: {
        'EARNING': 'earning',
        'FINANCE': 'finance',
        'MOST_PLAYED': 'most-played',
        'NOTIFICATION': 'notification',
        'RANKING': 'ranking',
        'TIMELINE': 'timeline'
    },

    TOURNAMENT_TYPE: {
        'knock_out': 'Eleme',
        'group': 'Grup',
        'ranking': 'Sıralama'
    },

    TOURNAMENT_STATUS: {
        'ACTIVE': 1,
        'CANCEL': 3,
        'CLOSE': 0,
        'OPEN': 2
    },

    USER: {
        'HOLDER': 'holder',
        'PLAYER': 'player'
    },

    USER_TYPE: {
        'player': 'Katılımcı',
        'holder': 'Düzenleyen'
    }
};
