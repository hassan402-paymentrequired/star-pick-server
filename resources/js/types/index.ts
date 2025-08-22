export interface User {
    id: number;
    username: string;
    email: string;
    avatar?: string;
    wallet: {
        balance: string;
    };
    created_at: string;
    updated_at: string;
}

export interface Peer {
    id: number;
    peer_id: string;
    name: string;
    amount: string;
    private: boolean;
    limit: number;
    sharing_ratio: number;
    status: 'open' | 'closed' | 'finished';
    winner_user_id?: number;
    created_by: {
        id: number;
        username: string;
    };
    users_count: number;
    created_at: string;
}

export interface PeerUser {
    id: number;
    user: {
        id: number;
        username: string;
        avatar?: string;
    };
    total_points: number;
    is_winner: boolean;
    created_at: string;
}

export interface PeerWithUsers extends Peer {
    users: PeerUser[];
}

export interface PageProps {
    auth: {
        user: User;
    };
    errors?: Record<string, string>;
    [key: string]: any;
}
