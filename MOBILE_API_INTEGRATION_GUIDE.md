# 📱 Mobile API Integration Guide - On the Night CMS

## 🚀 **Getting Started**

Your CMS is running locally at: `http://localhost:8000`  
API Base URL: `http://localhost:8000/api/v1`

---

## 🔧 **Step 1: Choose Your Platform**

### **Option A: Flutter (Recommended - Cross Platform)**
### **Option B: React Native (Cross Platform)**  
### **Option C: Native iOS (Swift)**
### **Option D: Native Android (Kotlin)**

---

## 📱 **Flutter Integration**

### **1. Create Flutter Project**
```bash
flutter create on_the_night_app
cd on_the_night_app
```

### **2. Add Dependencies**
```yaml
# pubspec.yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0              # API calls
  shared_preferences: ^2.2.0 # Token storage
  geolocator: ^10.1.0      # Location services
  cached_network_image: ^3.3.0 # Image caching
  provider: ^6.0.5         # State management
```

### **3. API Service Class**
```dart
// lib/services/api_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api/v1';
  String? _token;

  // Get stored token
  Future<String?> getToken() async {
    if (_token != null) return _token;
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
    return _token;
  }

  // Store token
  Future<void> storeToken(String token) async {
    _token = token;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  // Make authenticated request
  Future<Map<String, String>> _getHeaders() async {
    final token = await getToken();
    return {
      'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  // Login user
  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: await _getHeaders(),
      body: json.encode({'email': email, 'password': password}),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      await storeToken(data['access_token']);
      return data;
    } else {
      throw APIException('Login failed: ${response.statusCode}');
    }
  }

  // Get featured venues
  Future<List<Venue>> getFeaturedVenues({int limit = 10}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/venues/featured?limit=$limit'),
      headers: await _getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return (data['data'] as List).map((v) => Venue.fromJson(v)).toList();
    } else {
      throw APIException('Failed to load venues: ${response.statusCode}');
    }
  }

  // Get nearby venues
  Future<List<Venue>> getNearbyVenues(double lat, double lon, {double radius = 10}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/venues/nearby?latitude=$lat&longitude=$lon&radius=$radius'),
      headers: await _getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return (data['data'] as List).map((v) => Venue.fromJson(v)).toList();
    } else {
      throw APIException('Failed to load nearby venues: ${response.statusCode}');
    }
  }

  // Get venue details
  Future<Venue> getVenueDetails(int venueId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/venues/$venueId'),
      headers: await _getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return Venue.fromJson(data['data']);
    } else {
      throw APIException('Failed to load venue: ${response.statusCode}');
    }
  }

  // Get current deals
  Future<List<Deal>> getCurrentDeals({int limit = 20}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/deals/current?limit=$limit'),
      headers: await _getHeaders(),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return (data['data'] as List).map((v) => Deal.fromJson(v)).toList();
    } else {
      throw APIException('Failed to load deals: ${response.statusCode}');
    }
  }

  // Search venues
  Future<List<Venue>> searchVenues(String query, {
    String? locationId,
    List<String>? musicGenres,
    List<String>? drinkTypes,
  }) async {
    final uri = Uri.parse('$baseUrl/venues').replace(queryParameters: {
      'search': query,
      if (locationId != null) 'location_id': locationId,
      if (musicGenres != null) 'music_genres': musicGenres.join(','),
      if (drinkTypes != null) 'drink_types': drinkTypes.join(','),
    });

    final response = await http.get(uri, headers: await _getHeaders());

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return (data['data'] as List).map((v) => Venue.fromJson(v)).toList();
    } else {
      throw APIException('Search failed: ${response.statusCode}');
    }
  }

  // Create review
  Future<Review> createReview({
    required int venueId,
    required String reviewText,
    required int rating,
    String? reviewerName,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/reviews'),
      headers: await _getHeaders(),
      body: json.encode({
        'venue_id': venueId,
        'review_text': reviewText,
        'rating': rating,
        if (reviewerName != null) 'reviewer_name': reviewerName,
      }),
    );

    if (response.statusCode == 201) {
      final data = json.decode(response.body);
      return Review.fromJson(data['data']);
    } else {
      throw APIException('Failed to create review: ${response.statusCode}');
    }
  }
}

class APIException implements Exception {
  final String message;
  APIException(this.message);
  @override
  String toString() => message;
}
```

### **4. Data Models**
```dart
// lib/models/venue.dart
class Venue {
  final int id;
  final String name;
  final String description;
  final String address;
  final String city;
  final double? latitude;
  final double? longitude;
  final String status;
  final Map<String, String>? openingHours;
  final List<VenueImage> images;
  final Location? location;
  final List<DrinkType> drinkTypes;
  final List<MusicGenre> musicGenres;
  final VenueReviews reviews;
  final double? distance;

  Venue({
    required this.id,
    required this.name,
    required this.description,
    required this.address,
    required this.city,
    this.latitude,
    this.longitude,
    required this.status,
    this.openingHours,
    required this.images,
    this.location,
    required this.drinkTypes,
    required this.musicGenres,
    required this.reviews,
    this.distance,
  });

  factory Venue.fromJson(Map<String, dynamic> json) {
    return Venue(
      id: json['id'],
      name: json['name'],
      description: json['description'],
      address: json['address'],
      city: json['city'],
      latitude: json['latitude']?.toDouble(),
      longitude: json['longitude']?.toDouble(),
      status: json['status'],
      openingHours: json['opening_hours'] as Map<String, String>?,
      images: (json['images'] as List).map((i) => VenueImage.fromJson(i)).toList(),
      location: json['location'] != null ? Location.fromJson(json['location']) : null,
      drinkTypes: (json['drink_types'] as List).map((d) => DrinkType.fromJson(d)).toList(),
      musicGenres: (json['music_genres'] as List).map((m) => MusicGenre.fromJson(m)).toList(),
      reviews: VenueReviews.fromJson(json['reviews']),
      distance: json['distance']?.toDouble(),
    );
  }
}

class VenueImage {
  final String url;
  final String thumbnail;
  final String preview;
  final String collection;

  VenueImage({required this.url, required this.thumbnail, required this.preview, required this.collection});

  factory VenueImage.fromJson(Map<String, dynamic> json) {
    return VenueImage(
      url: json['url'],
      thumbnail: json['thumbnail'],
      preview: json['preview'],
      collection: json['collection'],
    );
  }
}

class Location {
  final int id;
  final String name;
  final String city;

  Location({required this.id, required this.name, required this.city});

  factory Location.fromJson(Map<String, dynamic> json) {
    return Location(
      id: json['id'],
      name: json['name'],
      city: json['city'],
    );
  }
}

class DrinkType {
  final int id;
  final String name;
  final String category;

  DrinkType({required this.id, required this.name, required this.category});

  factory DrinkType.fromJson(Map<String, dynamic> json) {
    return DrinkType(
      id: json['id'],
      name: json['name'],
      category: json['category'],
    );
  }
}

class MusicGenre {
  final int id;
  final String name;
  final String category;

  MusicGenre({required this.id, required this.name, required this.category});

  factory MusicGenre.fromJson(Map<String, dynamic> json) {
    return MusicGenre(
      id: json['id'],
      name: json['name'],
      category: json['category'],
    );
  }
}

class VenueReviews {
  final int count;
  final double averageRating;
  final List<Review> approvedReviews;

  VenueReviews({required this.count, required this.averageRating, required this.approvedReviews});

  factory VenueReviews.fromJson(Map<String, dynamic> json) {
    return VenueReviews(
      count: json['count'],
      averageRating: json['average_rating']?.toDouble() ?? 0.0,
      approvedReviews: (json['approved_reviews'] as List).map((r) => Review.fromJson(r)).toList(),
    );
  }
}

// lib/models/deal.dart
class Deal {
  final int id;
  final String title;
  final String description;
  final String dealType;
  final DateTime startDate;
  final DateTime endDate;
  final bool isActive;
  final bool isCurrent;
  final String timeRemaining;
  final String venueName;
  final List<DealImage> images;

  Deal({
    required this.id,
    required this.title,
    required this.description,
    required this.dealType,
    required this.startDate,
    required this.endDate,
    required this.isActive,
    required this.isCurrent,
    required this.timeRemaining,
    required this.venueName,
    required this.images,
  });

  factory Deal.fromJson(Map<String, dynamic> json) {
    return Deal(
      id: json['id'],
      title: json['title'],
      description: json['description'],
      dealType: json['deal_type'],
      startDate: DateTime.parse(json['start_date']),
      endDate: DateTime.parse(json['end_date']),
      isActive: json['is_active'],
      isCurrent: json['is_current'],
      timeRemaining: json['time_remaining'],
      venueName: json['venue_name'],
      images: (json['images'] as List).map((i) => DealImage.fromJson(i)).toList(),
    );
  }
}

class DealImage {
  final String url;
  final String thumbnail;

  DealImage({required this.url, required this.thumbnail});

  factory DealImage.fromJson(Map<String, dynamic> json) {
    return DealImage(url: json['url'], thumbnail: json['thumbnail']);
  }
}

// lib/models/review.dart
class Review {
  final int id;
  final String reviewText;
  final int rating;
  final String reviewerName;
  final bool isApproved;
  final DateTime createdAt;

  Review({
    required this.id,
    required this.reviewText,
    required this.rating,
    required this.reviewerName,
    required this.isApproved,
    required this.createdAt,
  });

  factory Review.fromJson(Map<String, dynamic> json) {
    return Review(
      id: json['id'],
      reviewText: json['review_text'],
      rating: json['rating'],
      reviewerName: json['reviewer_name'],
      isApproved: json['is_approved'],
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}
```

### **5. Example Screens**
```dart
// lib/screens/home_screen.dart
import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../services/api_service.dart';
import '../models/venue.dart';

class HomeScreen extends StatefulWidget {
  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ApiService _apiService = ApiService();
  List<Venue> featuredVenues = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadFeaturedVenues();
  }

  Future<void> _loadFeaturedVenues() async {
    try {
      final venues = await _apiService.getFeaturedVenues(limit: 10);
      setState(() {
        featuredVenues = venues;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error loading venues: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('ON THE NIGHT'),
        backgroundColor: Color(0xFFC41E41),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadFeaturedVenues,
              child: SingleChildScrollView(
                physics: AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Featured Venues Section
                    Padding(
                      padding: EdgeInsets.all(16),
                      child: Text(
                        'Featured Venues',
                        style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                      ),
                    ),
                    SizedBox(
                      height: 200,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        padding: EdgeInsets.symmetric(horizontal: 16),
                        itemCount: featuredVenues.length,
                        itemBuilder: (context, index) {
                          final venue = featuredVenues[index];
                          return Padding(
                            padding: EdgeInsets.only(right: 12),
                            child: SizedBox(
                              width: 150,
                              child: GestureDetector(
                                onTap: () => _navigateToVenueDetail(venue),
                                child: Card(
                                  clipBehavior: Clip.antiAlias,
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Expanded(
                                        flex: 3,
                                        child: venue.images.isNotEmpty
                                            ? CachedNetworkImage(
                                                imageUrl: venue.images.first.thumbnail,
                                                fit: BoxFit.cover,
                                                width: double.infinity,
                                                placeholder: (context, url) => Container(
                                                  color: Colors.grey[300],
                                                  child: Icon(Icons.local_bar),
                                                ),
                                                errorWidget: (context, url, error) => Container(
                                                  color: Colors.grey[300],
                                                  child: Icon(Icons.error),
                                                ),
                                              )
                                            : Container(
                                                color: Colors.grey[300],
                                                child: Icon(Icons.local_bar),
                                              ),
                                      ),
                                      Expanded(
                                        flex: 2,
                                        child: Padding(
                                          padding: EdgeInsets.all(8),
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                venue.name,
                                                style: TextStyle(fontWeight: FontWeight.bold),
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                              Text(
                                                venue.city,
                                                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                                              ),
                                              if (venue.distance != null)
                                                Text(
                                                  '${venue.distance!.toStringAsFixed(1)}km away',
                                                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                                                ),
                                            ],
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  void _navigateToVenueDetail(Venue venue) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => VenueDetailScreen(venueId: venue.id),
      ),
    );
  }
}

// lib/screens/venue_detail_screen.dart
class VenueDetailScreen extends StatefulWidget {
  final int venueId;

  VenueDetailScreen({required this.venueId});

  @override
  _VenueDetailScreenState createState() => _VenueDetailScreenState();
}

class _VenueDetailScreenState extends State<VenueDetailScreen> {
  final ApiService _apiService = ApiService();
  Venue? venue;
  List<Deal> deals = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadVenueDetails();
  }

  Future<void> _loadVenueDetails() async {
    try {
      final venueDetails = await _apiService.getVenueDetails(widget.venueId);
      setState(() {
        venue = venueDetails;
        isLoading = false;
      });
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error loading venue: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(venue?.name ?? 'Venue'),
        backgroundColor: Color(0xFFC41E41),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : venue == null
              ? Center(child: Text('Venue not found'))
              : SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Hero Image
                      if (venue!.images.isNotEmpty)
                        AspectRatio(
                          aspectRatio: 16/9,
                          child: CachedNetworkImage(
                            imageUrl: venue!.images.first.url,
                            fit: BoxFit.cover;
                            placeholder: (context, url) => Container(
                              color: Colors.grey[300],
                              child: Center(child: CircularProgressIndicator()),
                            ),
                          ),
                        ),
                      
                      // Venue Info
                      Padding(
                        padding: EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              venue!.name,
                              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                            ),
                            SizedBox(height: 8),
                            Text(
                              venue!.address,
                              style: TextStyle(fontSize: 16, color: Colors.grey[600]),
                            ),
                            if (venue!.distance != null) ...[
                              SizedBox(height: 4),
                              Text(
                                '${venue!.distance!.toStringAsFixed(1)}km away',
                                style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                              ),
                            ],
                            SizedBox(height: 16),
                            Text(
                              venue!.description,
                              style: TextStyle(fontSize: 16),
                            ),
                            
                            // Reviews Summary
                            SizedBox(height: 16),
                            Row(
                              children: [
                                Icon(Icons.star, color: Colors.amber),
                                SizedBox(width: 4),
                                Text('${venue!.reviews.averageRating.toStringAsFixed(1)}'),
                                SizedBox(width: 8),
                                Text('(${venue!.reviews.count} reviews)'),
                              ],
                            ),
                            
                            // Music Genres
                            SizedBox(height: 16),
                            Text('Music:', style: TextStyle(fontWeight: FontWeight.bold)),
                            Wrap(
                              spacing: 8,
                              children: venue!.musicGenres.map((genre) => Chip(label: Text(genre.name))).toList(),
                            ),
                            
                            // Drink Types
                            SizedBox(height: 16),
                            Text('Drinks:', style: TextStyle(fontWeight: FontWeight.bold)),
                            Wrap(
                              spacing: 8,
                              children: venue!.drinkTypes.map((drink) => Chip(label: Text(drink.name))).toList(),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
    );
  }
}
```

---

## 📱 **React Native Integration**

### **1. Create React Native Project**  
```bash
npx react-native init OnTheNightApp
cd OnTheNightApp
```

### **2. Install Dependencies**
```bash
npm install axios @react-native-async-storage/async-storage @react-native-geolocation/geolocation react-native-fast-image
```

### **3. API Service**
```javascript
// services/ApiService.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

class ApiService {
  constructor() {
    this.baseURL = 'http://localhost:8000/api/v1';
    this.token = null;
  }

  async setToken(token) {
    this.token = token;
    await AsyncStorage.setItem('auth_token', token);
  }

  async getToken() {
    if (!this.token) {
      this.token = await AsyncStorage.getItem('auth_token');
    }
    return this.token;
  }

  async makeRequest(method, endpoint, data = null) {
    const url = `${this.baseURL}${endpoint}`;
    const token = await this.getToken();
    
    const config = {
      method,
      url,
      headers: {
        'Content-Type': 'application/json',
        ...(token && { Authorization: `Bearer ${token}` }),
      },
      ...(data && { data }),
    };

    try {
      const response = await axios(config);
      return response.data;
    } catch (error) {
      throw new Error(`API Error: ${error.response?.status} - ${error.message}`);
    }
  }

  // Authentication
  async login(email, password) {
    const data = await this.makeRequest('POST', '/login', { email, password });
    await this.setToken(data.access_token);
    return data;
  }

  // Venues
  async getFeaturedVenues(limit = 10) {
    const data = await this.makeRequest('GET', `/venues/featured?limit=${limit}`);
    return data.data;
  }

  async getNearbyVenues(latitude, longitude, radius = 10) {
    const data = await this.makeRequest('GET', `/venues/nearby?latitude=${latitude}&longitude=${longitude}&radius=${radius}`);
    return data.data;
  }

  async getVenueDetails(venueId) {
    const data = await this.makeRequest('GET', `/venues/${venueId}`);
    return data.data;
  }

  async searchVenues(query, filters = {}) {
    const params = new URLSearchParams({ search: query, ...filters });
    const data = await this.makeRequest('GET', `/venues?${params}`);
            return data.data;
  }
  
  // Deals
  async getCurrentDeals(limit = 20) {
    const data = await this.makeRequest('GET', `/deals/current?limit=${limit}`);
    return data.data;
  }

  // Reviews
  async createReview(venueId, reviewText, rating, reviewerName = null) {
    const reviewData = { venue_id: venueId, review_text: reviewText, rating };
    if (reviewerName) reviewData.reviewer_name = reviewerName;
    
    const data = await this.makeRequest('POST', '/reviews', reviewData);
    return data.data;
  }
}

export default new ApiService();
```

### **4. Example React Native Component**
```javascript
// screens/HomeScreen.js
import React, { useState, useEffect } from 'react';
import { View, Text, FlatList, RefreshControl, Image } from 'react-native';
import FastImage from 'react-native-fast-image';
import ApiService from '../services/ApiService';

const HomeScreen = ({ navigation }) => {
  const [venues, setVenues] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadFeaturedVenues();
  }, []);

  const loadFeaturedVenues = async () => {
    try {
      setLoading(true);
      const featuredVenues = await ApiService.getFeaturedVenues(10);
      setVenues(featuredVenues);
    } catch (error) {
      console.error('Error loading venues:', error);
      alert('Error loading venues: ' + error.message);
    } finally {
      setLoading(false);
    }
  };

  const renderVenue = ({ item }) => (
    <TouchableOpacity 
      style={styles.venueCard}
      onPress={() => navigation.navigate('VenueDetail', { venueId: item.id })}
    >
      {item.images.length > 0 && (
        <FastImage
          style={styles.venueImage}
          source={{ uri: item.images[0].thumbnail }}
          resizeMode={FastImage.resizeMode.cover}
        />
      )}
      <View style={styles.venueInfo}>
        <Text style={styles.venueName}>{item.name}</Text>
        <Text style={styles.venueCity}>{item.city}</Text>
        {item.distance && (
          <Text style={styles.venueDistance}>{item.distance.toFixed(1)}km away</Text>
        )}
        <View style={styles.venueRating}>
          {[...Array(5)].map((_, i) => (
            <Text key={i} style={styles.star}>
              {i < Math.floor(item.reviews.average_rating) ? '⭐' : '☆'}
            </Text>
          ))}
          <Text style={styles.ratingText}>
            ({item.reviews.count} reviews)
          </Text>
        </View>
      </View>
    </TouchableOpacity>
  );

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Featured Venues</Text>
      <FlatList
        data={venues}
        renderItem={renderVenue}
        keyExtractor={(item) => item.id.toString()}
        refreshControl={
          <RefreshControl refreshing={loading} onRefresh={loadFeaturedVenues} />
        }
        style={styles.list}
      />
    </View>
  );
};

const styles = {
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
    paddingTop: 50,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 20,
    color: '#C41E41',
  },
  venueCard: {
    backgroundColor: 'white',
    margin: 10,
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
  },
  venueImage: {
    width: '100%',
    height: 150,
    borderTopLeftRadius: 10,
    borderTopRightRadius: 10,
  },
  venueInfo: {
    padding: 15,
  },
  venueName: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  venueCity: {
    fontSize: 14,
    color: '#666',
    marginBottom: 5,
  },
  venueDistance: {
    fontSize: 14,
    color: '#C41E41',
    fontWeight: 'bold',
  },
  venueRating: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 10,
  },
  star: {
    fontSize: 16,
  },
  ratingText: {
    fontSize: 12,
    color: '#666',
    marginLeft: 5,
  },
};

export default HomeScreen;
```

---

## 🔒 **Authentication Flow**

### **Login Screen Implementation**
```dart
// Flutter Login Screen
class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController(text: 'mobile@onthenight.app');
  final _passwordController = TextEditingController(text: 'password');
  final ApiService _apiService = ApiService();
  bool _isLoading = false;

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);
    
    try {
      await _apiService.login(_emailController.text, _passwordController.text);
      Navigator.pushReplacementNamed(context, '/home');
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Login failed: $e')),
      );
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color(0xFFC41E41),
      body: Center(
        child: Card(
          margin: EdgeInsets.all(32),
          child: Padding(
            padding: EdgeInsets.all(24),
            child: Form(
              key: _formKey,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    'ON THE NIGHT',
                    style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                  ),
                  SizedBox(height: 32),
                  TextFormField(
                    controller: _emailController,
                    decoration: InputDecoration(labelText: 'Email'),
                    keyboardType: TextInputType.emailAddress,
                    validator: (value) => value?.isEmpty ?? true ? 'Email required' : null,
                  ),
                  SizedBox(height: 16),
                  TextFormField(
                    controller: _passwordController,
                    decoration: InputDecoration(labelText: 'Password'),
                    obscureText: true,
                    validator: (value) => value?.isEmpty ?? true ? 'Password required' : null,
                  ),
                  SizedBox(height: 24),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : _handleLogin,
                      child: _isLoading 
                        ? CircularProgressIndicator(color: Colors.white)
                        : Text('Login',
                        style: TextStyle(color: Color(0xFFC41E41)),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
```

---

## 🛠️ **Key Integration Points**

### **1. Environment Configuration**
```dart
// lib/config/app_config.dart
class AppConfig {
  static const String apiBaseUrl = 'http://localhost:8000/api/v1';
  static const String appColor = '#C41E41';
  
  // For production, change to your deployed API URL
  static const String productionApiBaseUrl = 'https://your-domain.com/api/v1';
}
```

### **2. Error Handling**
```dart
// lib/utils/error_handler.dart
class ErrorHandler {
  static String getErrorMessage(dynamic error) {
    if (error is APIException) {
      return error.toString();
    } else if (error.toString().contains('No internet')) {
      return 'No internet connection';
    } else {
      return 'Something went wrong. Please try again.';
    }
  }

  static void showError(BuildContext context, dynamic error) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(getErrorMessage(error))),
    );
  }
}
```

### **3. Location Services**
```dart
// lib/services/location_service.dart
import 'package:geolocator/geolocator.dart';

class LocationService {
  static Future<Position?> getCurrentLocation() async {
    try {
      final position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );
      return position;
    } catch (e) {
      return null;
    }
  }

  static Future<bool> requestPermission() async {
    LocationPermission permission = await Geolocator.checkPermission();
    
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }
    
    return permission == LocationPermission.whileInUse || 
           permission == LocationPermission.always;
  }
}
```

---

## 🚀 **Testing Your Integration**

### **1. Test API Endpoints**
```bash
# Check if API is running
curl http://localhost:8000/api/v1/venues/featured

# Test authentication
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"mobile@onthenight.app","password":"password"}'
```

### **2. Mobile App Testing**
1. **Start your CMS**: Ensure Laravel server is running
2. **Run mobile app**: Launch Flutter/React Native app
3. **Test login**: Use `mobile@onthenight.app` / `password`
4. **Browse venues**: Test venue loading and navigation
5. **Test location**: Enable location services for nearby venues

---

## 📱 **Production Deployment**

### **1. Update API URL**
```dart
// For production deployment
class AppConfig {
  static const String apiBaseUrl = 'https://your-domain.com/api/v1';
}
```

### **2. Image URLs**
Make sure images are served via HTTPS in production and update storage URLs in Laravel.

### **3. Security**
- Implement certificate pinning
- Use proper authentication with refresh tokens
- Validate API responses before displaying

This guide provides everything you need to integrate your Figma design with the On the Night CMS API! 🎉
