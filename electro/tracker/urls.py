# tracker/urls.py
from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import MonthlyNumberViewSet

router = DefaultRouter()
router.register(r'monthly_numbers', MonthlyNumberViewSet)

urlpatterns = [
    path('', include(router.urls)),
]