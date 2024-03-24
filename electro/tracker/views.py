from django.shortcuts import render

# Create your views here.
# tracker/views.py
from rest_framework import viewsets
from .models import MonthlyNumber
from .serializers import MonthlyNumberSerializer

class MonthlyNumberViewSet(viewsets.ModelViewSet):
    queryset = MonthlyNumber.objects.all()
    serializer_class = MonthlyNumberSerializer