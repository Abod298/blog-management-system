<script setup>
import { ref , onMounted } from 'vue'
import PostHeader from '@/components/PostHeader.vue'
import CommentSection from '@/components/CommentSection.vue'
import axios from 'axios';
import { useRoute } from 'vue-router';

const route = useRoute();
const post = ref({
  id:'',
  image: '',
  title: '',
  body: '',
  author: { name: '', last_name: '', avatar: '' },
  categories: [],
  comments: [],
  created_at: '',
  updated_at: '',
});

const formatDate = (date) => new Date(date).toLocaleString();

const fetchPostData = async () => {
  try {
    const response = await axios.get(`/api/posts/${route.params.slug}`);

    const postData = response.data;

    post.value = {
      id:postData.id,
      image: postData.image,
      title: postData.title,
      body: postData.body,
      author: postData.author,
      categories: postData.categories,
      comments: postData.comments,
      created_at: postData.created_at,
      updated_at: postData.updated_at,
    };
  } catch (error) {
    console.error('Error fetching post data:', error);
  }
};

onMounted(fetchPostData);

const addComment = async (commentBody) => {
  try {
    const response = await axios.post('/api/comments', {
      body: commentBody,
      post_id: post.value.id
    });
    const newComment = response.data;
    post.value.comments.push({
      id: newComment.id,
      body: newComment.body,
      user: {
        name: newComment.user.name,
        last_name: newComment.user.last_name
      },
      confirmed_by: null, 
      created_at: newComment.created_at,
      updated_at: newComment.updated_at
    });
  } catch (error) {
    console.error('Error adding comment:', error);
  }
};
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-8">
    <PostHeader :post="post" />
    <CommentSection 
      :comments="post.comments"
      @add-comment="addComment"
    />
  </div>
</template>

<style scoped>

</style>