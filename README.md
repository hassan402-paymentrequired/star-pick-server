import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  LayoutAnimation,
  Platform,
  UIManager,
  ScrollView,
  Pressable,
  Image,
} from 'react-native';
import { AntDesign } from '@expo/vector-icons';
import { BlurView } from 'expo-blur';

if (Platform.OS === 'android') {
  UIManager.setLayoutAnimationEnabledExperimental?.(true);
}

// Define the user interface
interface User {
  id: number;
  username: string;
  avatar: string;
  email: string;
  created_at: string;
  pivot?: {
    peer_id: number;
    user_id: number;
    created_at: string;
    updated_at: string;
  };
}

interface PeerUserCardProps {
  user?: User;
}

const PeerUserCard = ({ user }: PeerUserCardProps) => {
  const [expanded, setExpanded] = useState(false);

  const toggleExpand = () => {
    LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);
    setExpanded(!expanded);
  };

  // Use provided user data or fallback to mock data
  const userData = user || {
    username: 'Owner',
    avatar: 'https://api.dicebear.com/9.x/bottts/png?seed=Felix&backgroundColor=b6e3f4',
    created_at: new Date().toISOString(),
  };

  // Calculate time since joined
  const getTimeSinceJoined = (createdAt: string) => {
    const now = new Date();
    const joined = new Date(createdAt);
    const diffInMinutes = Math.floor((now.getTime() - joined.getTime()) / (1000 * 60));

    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes} mins ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)} hours ago`;
    return `${Math.floor(diffInMinutes / 1440)} days ago`;
  };

  // Mock players data (this would come from the user's selected players)
  const mockPlayers = [
    // Star 1
    {
      star: 1,
      type: 'Main',
      name: 'Vinicius Jr',
      goals: 1,
      assists: 1,
      shots: 3,
      onTarget: 2,
      crosses: 2,
      tackles: 1,
      saves: 0,
      cleanSheet: 0,
      yellowCard: 0,
      redCard: 0,
      total: 10,
    },
    {
      star: 1,
      type: 'Sub',
      name: 'Osimhen',
      goals: 0,
      assists: 0,
      shots: 1,
      onTarget: 0,
      crosses: 0,
      tackles: 1,
      saves: 0,
      cleanSheet: 0,
      yellowCard: 1,
      redCard: 0,
      total: 3,
    },
    // Star 2
    {
      star: 2,
      type: 'Main',
      name: 'Bellingham',
      goals: 2,
      assists: 0,
      shots: 4,
      onTarget: 3,
      crosses: 1,
      tackles: 2,
      saves: 0,
      cleanSheet: 0,
      yellowCard: 0,
      redCard: 0,
      total: 12,
    },
    // Star 3
    {
      star: 3,
      type: 'Main',
      name: 'Modric',
      goals: 0,
      assists: 1,
      shots: 2,
      onTarget: 1,
      crosses: 3,
      tackles: 3,
      saves: 0,
      cleanSheet: 0,
      yellowCard: 0,
      redCard: 0,
      total: 9,
    },
    // Star 4
    {
      star: 4,
      type: 'Main',
      name: 'Casemiro',
      goals: 0,
      assists: 0,
      shots: 1,
      onTarget: 1,
      crosses: 0,
      tackles: 5,
      saves: 0,
      cleanSheet: 1,
      yellowCard: 1,
      redCard: 0,
      total: 8,
    },
    // Star 5
    {
      star: 5,
      type: 'Main',
      name: 'Allison Becker',
      goals: 0,
      assists: 0,
      shots: 0,
      onTarget: 0,
      crosses: 0,
      tackles: 0,
      saves: 6,
      cleanSheet: 1,
      yellowCard: 0,
      redCard: 0,
      total: 11,
    },
  ];

  // Group players by star
  const groupedByStar = Array.from({ length: 5 }, (_, i) => {
    const star = i + 1;
    return {
      star,
      players: mockPlayers.filter((p) => p.star === star),
    };
  });

  return (
    <View style={{ marginBottom: 12 }}>
      <BlurView intensity={50} tint="dark" style={styles.card}>
        <Pressable onPress={toggleExpand}>
          <View style={styles.header}>
            <Image
              source={{ uri: userData.avatar }}
              style={styles.avatar}
              defaultSource={{
                uri: 'https://api.dicebear.com/9.x/bottts/png?seed=default&backgroundColor=b6e3f4',
              }}
            />

            <View style={{ marginLeft: 10, flex: 1 }}>
              <Text style={styles.username}>@{userData.username}</Text>
              <Text style={styles.subInfo}>
                5⭐ squad · Joined {getTimeSinceJoined(userData.created_at)}
              </Text>
            </View>

            <AntDesign
              name={expanded ? 'up' : 'down'}
              size={16}
              color="white"
              style={{ marginLeft: 'auto' }}
            />
          </View>
        </Pressable>

        {expanded && (
          <ScrollView horizontal showsHorizontalScrollIndicator={true} style={styles.tableWrapper}>
            <View>
              {groupedByStar.map(({ star, players }) =>
                players.length > 0 ? (
                  <View key={star} style={{ marginBottom: 10 }}>
                    <Text style={styles.starLabel}>⭐ Star {star} Players</Text>

                    <View style={styles.tableHeader}>
                      <Text style={styles.cell}>Type</Text>
                      <Text style={[styles.cell, { width: 100 }]}>Player</Text>
                      <Text style={styles.cell}>G</Text>
                      <Text style={styles.cell}>A</Text>
                      <Text style={styles.cell}>Sh</Text>
                      <Text style={styles.cell}>On T</Text>
                      <Text style={styles.cell}>Cross</Text>
                      <Text style={styles.cell}>Tack</Text>
                      <Text style={styles.cell}>Save</Text>
                      <Text style={styles.cell}>CS</Text>
                      <Text style={styles.cell}>YC</Text>
                      <Text style={styles.cell}>RC</Text>
                      <Text style={styles.cell}>Total</Text>
                    </View>

                    {players.map((player, index) => (
                      <View key={index} style={styles.tableRow}>
                        <Text style={styles.cell}>{player.type}</Text>
                        <Text style={[styles.cell, { width: 100 }]}>{player.name}</Text>
                        <Text style={styles.cell}>{player.goals}</Text>
                        <Text style={styles.cell}>{player.assists}</Text>
                        <Text style={styles.cell}>{player.shots}</Text>
                        <Text style={styles.cell}>{player.onTarget}</Text>
                        <Text style={styles.cell}>{player.crosses}</Text>
                        <Text style={styles.cell}>{player.tackles}</Text>
                        <Text style={styles.cell}>{player.saves}</Text>
                        <Text style={styles.cell}>{player.cleanSheet}</Text>
                        <Text style={styles.cell}>{player.yellowCard}</Text>
                        <Text style={styles.cell}>{player.redCard}</Text>
                        <Text style={styles.cell}>{player.total}</Text>
                      </View>
                    ))}
                  </View>
                ) : null
              )}
            </View>
          </ScrollView>
        )}
      </BlurView>
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    borderRadius: 8,
    padding: 12,
    borderWidth: 1,
    borderColor: '#1F2937',
    overflow: 'hidden',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  avatar: {
    width: 40,
    height: 40,
    borderRadius: 20,
    borderColor: '#1F2937',
    borderWidth: 1,
  },
  username: {
    fontSize: 16,
    color: 'white',
    fontWeight: '600',
  },
  subInfo: {
    color: 'gray',
    fontSize: 12,
  },
  tableWrapper: {
    marginTop: 12,
  },
  starLabel: {
    color: '#fff',
    fontWeight: '700',
    marginBottom: 6,
    marginTop: 10,
  },
  tableHeader: {
    flexDirection: 'row',
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
    paddingVertical: 6,
    borderRadius: 4,
  },
  tableRow: {
    flexDirection: 'row',
    paddingVertical: 6,
    borderBottomWidth: 0.5,
    borderColor: 'rgba(255,255,255,0.1)',
  },
  cell: {
    color: 'white',
    fontSize: 12,
    width: 60,
    textAlign: 'center',
  },
});

export default PeerUserCard;
